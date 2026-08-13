<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Payment;
use App\Models\Expense;
use App\Models\Establishment;
use App\Models\LegalSpecialty;

use Carbon\Carbon;

class FinancialReportController extends Controller
{
    public function index()
    {
        $establishments = Establishment::orderBy('name')->get();

        $specialties = LegalSpecialty::orderBy('name')->get();

        return view(
            'reports.financial.index',
            compact(
                'establishments',
                'specialties'
            )
        );
    }

    public function datatable(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Query base con filtros de fecha y sede
        |--------------------------------------------------------------------------
        */

        $paymentsQuery = Payment::query();
        $expensesQuery = Expense::query();

        // Fechas
        if ($request->date_from) {
            $paymentsQuery->whereDate('payment_date', '>=', $request->date_from);
            $expensesQuery->whereDate('expense_date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $paymentsQuery->whereDate('payment_date', '<=', $request->date_to);
            $expensesQuery->whereDate('expense_date', '<=', $request->date_to);
        }

        // Sede
        if ($request->establishment_id) {
            $paymentsQuery->whereHas('consultation', function ($q) use ($request) {
                $q->where('establishment_id', $request->establishment_id);
            });
            $expensesQuery->where('establishment_id', $request->establishment_id);
        }

        // Especialidad
        if ($request->specialty_id) {
            $paymentsQuery->whereHas('consultation', function ($q) use ($request) {
                $q->where('legal_specialty_id', $request->specialty_id);
            });
            $expensesQuery->whereHas('case', function ($q) use ($request) {
                $q->where('legal_specialty_id', $request->specialty_id);
            });
        }

        // Tipo servicio
        if ($request->service_type) {
            $paymentsQuery->whereHas('consultation', function ($q) use ($request) {
                $q->where('service_type', $request->service_type);
            });
            $expensesQuery->whereHas('case', function ($q) use ($request) {
                $q->where('service_type', $request->service_type);
            });
        }

        /*
        |--------------------------------------------------------------------------
        | KPIs - Calculados directamente en SQL
        |--------------------------------------------------------------------------
        */

        $income = (clone $paymentsQuery)->sum('amount');
        $expense = (clone $expensesQuery)->sum('amount');
        $profit = $income - $expense;
        $netFlow = $profit;

        $margin = $income > 0
            ? round(($profit * 100) / $income, 2)
            : 0;

        $consultationCount = (clone $paymentsQuery)
            ->select('consultation_id')
            ->distinct()
            ->count();

        $avgTicket = $consultationCount > 0
            ? round($income / $consultationCount, 2)
            : 0;

        $roi = $expense > 0
            ? round($income / $expense, 2)
            : 0;

        /*
        |--------------------------------------------------------------------------
        | Gráfico: Ingresos vs Gastos mensuales (GROUP BY en SQL)
        |--------------------------------------------------------------------------
        */

        $incomeMonths = (clone $paymentsQuery)
            ->selectRaw('DATE_FORMAT(payment_date, "%Y-%m") as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $expenseMonths = (clone $expensesQuery)
            ->selectRaw('DATE_FORMAT(expense_date, "%Y-%m") as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Unir ingresos y gastos por mes
        $allMonths = $incomeMonths->pluck('total', 'month')
            ->union($expenseMonths->pluck('total', 'month')->mapWithKeys(function ($total, $month) {
                return [$month => ['expense' => $total]];
            }));

        $incomeExpenseLabels = [];
        $incomeExpenseIncome = [];
        $incomeExpenseExpense = [];

        $months = $incomeMonths->pluck('month')->merge($expenseMonths->pluck('month'))->unique()->sort();

        foreach ($months as $month) {
            $incomeExpenseLabels[] = Carbon::parse($month . '-01')->translatedFormat('M Y');
            $incomeExpenseIncome[] = round($incomeMonths->firstWhere('month', $month)['total'] ?? 0, 2);
            $incomeExpenseExpense[] = round($expenseMonths->firstWhere('month', $month)['total'] ?? 0, 2);
        }

        /*
        |--------------------------------------------------------------------------
        | Gastos por categoría (GROUP BY en SQL)
        |--------------------------------------------------------------------------
        */

        $expenseCategoriesQuery = (clone $expensesQuery)
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get();

        $expenseCategories = [];
        foreach ($expenseCategoriesQuery as $row) {
            $label = config('options.expense_categories')[$row->category] ?? $row->category;
            $expenseCategories[$label] = round($row->total, 2);
        }

        /*
        |--------------------------------------------------------------------------
        | Ingresos por sede (JOIN en SQL)
        |--------------------------------------------------------------------------
        */

        $incomeByEstablishmentQuery = (clone $paymentsQuery)
            ->selectRaw('COALESCE(establishments.name, "Sin sede") as name, SUM(payments.amount) as total')
            ->leftJoin('establishments', 'payments.establishment_id', '=', 'establishments.id')
            ->groupBy('establishments.id')
            ->get();

        $incomeByEstablishment = [];
        foreach ($incomeByEstablishmentQuery as $row) {
            $incomeByEstablishment[$row->name] = round($row->total, 2);
        }

        /*
        |--------------------------------------------------------------------------
        | Ingresos por especialidad (JOIN en SQL)
        |--------------------------------------------------------------------------
        */

        $incomeBySpecialtyQuery = (clone $paymentsQuery)
            ->selectRaw('COALESCE(legal_specialties.name, "Sin especialidad") as name, SUM(payments.amount) as total')
            ->leftJoin('consultations', 'payments.consultation_id', '=', 'consultations.id')
            ->leftJoin('legal_specialties', 'consultations.legal_specialty_id', '=', 'legal_specialties.id')
            ->groupBy('legal_specialties.id')
            ->get();

        $incomeBySpecialty = [];
        foreach ($incomeBySpecialtyQuery as $row) {
            $incomeBySpecialty[$row->name] = round($row->total, 2);
        }

        /*
        |--------------------------------------------------------------------------
        | Utilidad mensual
        |--------------------------------------------------------------------------
        */

        $profitLabels = $incomeExpenseLabels;
        $profitValues = [];
        foreach ($months as $month) {
            $profitValues[] = round(
                ($incomeMonths->firstWhere('month', $month)['total'] ?? 0)
                - ($expenseMonths->firstWhere('month', $month)['total'] ?? 0),
                2
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Charts
        |--------------------------------------------------------------------------
        */

        $charts = [
            'income_expense' => [
                'labels' => $incomeExpenseLabels,
                'income' => $incomeExpenseIncome,
                'expense' => $incomeExpenseExpense,
            ],
            'expense_category' => [
                'labels' => array_keys($expenseCategories),
                'values' => array_values($expenseCategories),
            ],
            'establishments' => [
                'labels' => array_keys($incomeByEstablishment),
                'values' => array_values($incomeByEstablishment),
            ],
            'specialties' => [
                'labels' => array_keys($incomeBySpecialty),
                'values' => array_values($incomeBySpecialty),
            ],
            'profit_monthly' => [
                'labels' => $profitLabels,
                'values' => $profitValues,
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | Resumen por sede (JOIN en SQL)
        |--------------------------------------------------------------------------
        */

        $establishmentSummaryQuery = (clone $paymentsQuery)
            ->selectRaw('COALESCE(establishments.name, "Sin sede") as name, SUM(payments.amount) as income')
            ->leftJoin('establishments', 'payments.establishment_id', '=', 'establishments.id')
            ->groupBy('establishments.id')
            ->get();

        $establishmentSummary = [];
        foreach ($establishmentSummaryQuery as $row) {
            $establishmentSummary[$row->name] = [
                'establishment' => $row->name,
                'income' => round($row->income, 2),
                'expense' => 0,
                'profit' => 0,
                'margin' => 0,
            ];
        }

        // Agregar gastos por sede
        $expenseByEstablishmentQuery = (clone $expensesQuery)
            ->selectRaw('COALESCE(establishments.name, "Sin sede") as name, SUM(expenses.amount) as total')
            ->leftJoin('establishments', 'expenses.establishment_id', '=', 'establishments.id')
            ->groupBy('establishments.id')
            ->get();

        foreach ($expenseByEstablishmentQuery as $row) {
            if (!isset($establishmentSummary[$row->name])) {
                $establishmentSummary[$row->name] = [
                    'establishment' => $row->name,
                    'income' => 0,
                    'expense' => 0,
                    'profit' => 0,
                    'margin' => 0,
                ];
            }
            $establishmentSummary[$row->name]['expense'] = round($row->total, 2);
        }

        foreach ($establishmentSummary as &$row) {
            $row['profit'] = round($row['income'] - $row['expense'], 2);
            $row['margin'] = $row['income'] > 0
                ? round(($row['profit'] * 100) / $row['income'], 2)
                : 0;
        }
        unset($row);

        /*
        |--------------------------------------------------------------------------
        | Movimientos financieros
        |--------------------------------------------------------------------------
        */

        // Pagos
        $paymentRows = (clone $paymentsQuery)
            ->with('consultation.establishment', 'consultation.client')
            ->orderBy('payment_date', 'desc')
            ->get();

        // Gastos
        $expenseRows = (clone $expensesQuery)
            ->with('establishment', 'case.client')
            ->orderBy('expense_date', 'desc')
            ->get();

        // Combinar y ordenar por fecha
        $rows = [];

        foreach ($paymentRows as $payment) {
            $rows[] = [
                'date' => Carbon::parse($payment->payment_date)->format('d/m/Y'),
                'date_raw' => $payment->payment_date,
                'type' => '<span class="badge bg-success">Ingreso</span>',
                'concept' => 'Pago consulta #' . $payment->consultation_id,
                'establishment' => optional($payment->consultation?->establishment)->name ?? 'Sin sede',
                'client' => optional($payment->consultation?->client)->full_name ?? '-',
                'amount' => number_format($payment->amount, 2),
                'amount_raw' => $payment->amount,
            ];
        }

        foreach ($expenseRows as $expenseItem) {
            $rows[] = [
                'date' => Carbon::parse($expenseItem->expense_date)->format('d/m/Y'),
                'date_raw' => $expenseItem->expense_date,
                'type' => '<span class="badge bg-danger">Gasto</span>',
                'concept' => $expenseItem->description ?: (config('options.expense_categories')[$expenseItem->category] ?? $expenseItem->category),
                'establishment' => optional($expenseItem->establishment)->name ?? 'Sin sede',
                'client' => optional(optional($expenseItem->case)->client)->full_name ?? '-',
                'amount' => number_format($expenseItem->amount, 2),
                'amount_raw' => $expenseItem->amount,
            ];
        }

        // Ordenar por fecha descendente
        usort($rows, function ($a, $b) {
            return strtotime($b['date_raw']) <=> strtotime($a['date_raw']);
        });

        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'summary' => [
                'income' => round($income, 2),
                'expense' => round($expense, 2),
                'profit' => round($profit, 2),
                'net_flow' => round($netFlow, 2),
                'margin' => $margin,
                'avg_ticket' => $avgTicket,
                'roi' => $roi,
            ],
            'charts' => $charts,
            'establishment_summary' => array_values($establishmentSummary),
            'data' => $rows,
        ]);
    }
}
