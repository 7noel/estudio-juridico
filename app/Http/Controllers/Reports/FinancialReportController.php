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
        | Payments
        |--------------------------------------------------------------------------
        */

        $payments = Payment::with([
            'consultation.establishment',
            'consultation.client',
            'consultation.specialty',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Expenses
        |--------------------------------------------------------------------------
        */

        $expenses = Expense::with([
            'case.client',
            'establishment'
        ]);

        /*
        |--------------------------------------------------------------------------
        | Fechas
        |--------------------------------------------------------------------------
        */

        if ($request->date_from) {

            $payments->whereDate(
                'payment_date',
                '>=',
                $request->date_from
            );

            $expenses->whereDate(
                'expense_date',
                '>=',
                $request->date_from
            );

        }

        if ($request->date_to) {

            $payments->whereDate(
                'payment_date',
                '<=',
                $request->date_to
            );

            $expenses->whereDate(
                'expense_date',
                '<=',
                $request->date_to
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Sede
        |--------------------------------------------------------------------------
        */

        if ($request->establishment_id) {

            $payments->whereHas(
                'consultation',
                function ($q) use ($request) {

                    $q->where(
                        'establishment_id',
                        $request->establishment_id
                    );

                }
            );

            $expenses->where(
                'establishment_id',
                $request->establishment_id
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Especialidad
        |--------------------------------------------------------------------------
        */

        if ($request->specialty_id) {

            $payments->whereHas(
                'consultation',
                function ($q) use ($request) {

                    $q->where(
                        'legal_specialty_id',
                        $request->specialty_id
                    );

                }
            );

            $expenses->whereHas(
                'case',
                function ($q) use ($request) {

                    $q->where(
                        'legal_specialty_id',
                        $request->specialty_id
                    );

                }
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Tipo servicio
        |--------------------------------------------------------------------------
        */

        if ($request->service_type) {

            $payments->whereHas(
                'consultation',
                function ($q) use ($request) {

                    $q->where(
                        'service_type',
                        $request->service_type
                    );

                }
            );

            $expenses->whereHas(
                'case',
                function ($q) use ($request) {

                    $q->where(
                        'service_type',
                        $request->service_type
                    );

                }
            );

        }

        $payments = $payments->get();

        $expenses = $expenses->get();

        /*
        |--------------------------------------------------------------------------
        | KPI
        |--------------------------------------------------------------------------
        */

        $income =
            $payments->sum('amount');

        $expense =
            $expenses->sum('amount');

        $profit =
            $income - $expense;

        $netFlow =
            $profit;

        $margin =
            $income > 0
                ? round(
                    ($profit * 100)
                    / $income,
                    2
                )
                : 0;

        $consultationCount =
            $payments
                ->pluck('consultation_id')
                ->unique()
                ->count();

        $avgTicket =
            $consultationCount > 0

                ? round(
                    $income
                    /
                    $consultationCount,
                    2
                )

                : 0;

        $roi =
            $expense > 0

                ? round(
                    $income
                    /
                    $expense,
                    2
                )

                : 0;

        /*
        |--------------------------------------------------------------------------
        | Gráfico
        | Ingresos vs Gastos mensual
        |--------------------------------------------------------------------------
        */

        $incomeExpenseMonths = [];

        foreach ($payments as $payment) {

            $month = Carbon::parse(
                $payment->payment_date
            )->format('Y-m');

            if (!isset($incomeExpenseMonths[$month])) {

                $incomeExpenseMonths[$month] = [

                    'income' => 0,
                    'expense' => 0,

                ];

            }

            $incomeExpenseMonths[$month]['income']
                += $payment->amount;

        }

        foreach ($expenses as $item) {

            $month = Carbon::parse(
                $item->expense_date
            )->format('Y-m');

            if (!isset($incomeExpenseMonths[$month])) {

                $incomeExpenseMonths[$month] = [

                    'income' => 0,
                    'expense' => 0,

                ];

            }

            $incomeExpenseMonths[$month]['expense']
                += $item->amount;

        }

        ksort($incomeExpenseMonths);

        $incomeExpenseLabels = [];
        $incomeExpenseIncome = [];
        $incomeExpenseExpense = [];

        foreach ($incomeExpenseMonths as $month => $values) {

            $incomeExpenseLabels[] =
                Carbon::parse($month . '-01')
                    ->translatedFormat('M Y');

            $incomeExpenseIncome[] =
                round($values['income'], 2);

            $incomeExpenseExpense[] =
                round($values['expense'], 2);

        };

        /*
        |--------------------------------------------------------------------------
        | Gastos por categoría
        |--------------------------------------------------------------------------
        */

        $expenseCategories = [];

        foreach ($expenses as $item) {

            $label =
                config(
                    'options.expense_categories'
                )[$item->category]
                ??
                $item->category;

            if (!isset($expenseCategories[$label])) {

                $expenseCategories[$label] = 0;

            }

            $expenseCategories[$label]
                += $item->amount;

        }

        /*
        |--------------------------------------------------------------------------
        | Ingresos por sede
        |--------------------------------------------------------------------------
        */

        $incomeByEstablishment = [];

        foreach ($payments as $payment) {

            $name =
                optional(
                    $payment->consultation
                        ->establishment
                )->name
                ?? 'Sin sede';

            if (!isset(
                $incomeByEstablishment[$name]
            )) {

                $incomeByEstablishment[$name]
                    = 0;

            }

            $incomeByEstablishment[$name]
                += $payment->amount;

        }

        /*
        |--------------------------------------------------------------------------
        | Ingresos por especialidad
        |--------------------------------------------------------------------------
        */

        $incomeBySpecialty = [];

        foreach ($payments as $payment) {

            $name =
                optional(
                    $payment->consultation
                        ->specialty
                )->name
                ?? 'Sin especialidad';

            if (!isset(
                $incomeBySpecialty[$name]
            )) {

                $incomeBySpecialty[$name]
                    = 0;

            }

            $incomeBySpecialty[$name]
                += $payment->amount;

        }

        /*
        |--------------------------------------------------------------------------
        | Utilidad mensual
        |--------------------------------------------------------------------------
        */

        $profitMonths = [];

        foreach ($incomeExpenseMonths as $month => $values) {

            $profitMonths[$month] =
                $values['income']
                -
                $values['expense'];

        }

        $profitLabels = [];
        $profitValues = [];

        foreach ($profitMonths as $month => $value) {

            $profitLabels[] =
                Carbon::parse(
                    $month . '-01'
                )->translatedFormat(
                    'M Y'
                );

            $profitValues[] =
                round($value, 2);

        }

        /*
        |--------------------------------------------------------------------------
        | Charts
        |--------------------------------------------------------------------------
        */

        $charts = [

            'income_expense' => [

                'labels' =>
                    $incomeExpenseLabels,

                'income' =>
                    $incomeExpenseIncome,

                'expense' =>
                    $incomeExpenseExpense,

            ],

            'expense_category' => [

                'labels' =>
                    array_keys(
                        $expenseCategories
                    ),

                'values' =>
                    array_values(
                        $expenseCategories
                    ),

            ],

            'establishments' => [

                'labels' =>
                    array_keys(
                        $incomeByEstablishment
                    ),

                'values' =>
                    array_values(
                        $incomeByEstablishment
                    ),

            ],

            'specialties' => [

                'labels' =>
                    array_keys(
                        $incomeBySpecialty
                    ),

                'values' =>
                    array_values(
                        $incomeBySpecialty
                    ),

            ],

            'profit_monthly' => [

                'labels' =>
                    $profitLabels,

                'values' =>
                    $profitValues,

            ],

        ];

        /*
        |--------------------------------------------------------------------------
        | Resumen por sede
        |--------------------------------------------------------------------------
        */

        $establishmentSummary = [];

        foreach ($payments as $payment) {

            $name =
                optional(
                    $payment->consultation
                        ->establishment
                )->name
                ?? 'Sin sede';

            if (!isset(
                $establishmentSummary[$name]
            )) {

                $establishmentSummary[$name] = [

                    'establishment' => $name,

                    'income' => 0,

                    'expense' => 0,

                    'profit' => 0,

                    'margin' => 0,

                ];

            }

            $establishmentSummary[$name]['income']
                += $payment->amount;

        }

        foreach ($expenses as $expenseItem) {

            $name =
                optional(
                    $expenseItem->establishment
                )->name
                ?? 'Sin sede';

            if (!isset(
                $establishmentSummary[$name]
            )) {

                $establishmentSummary[$name] = [

                    'establishment' => $name,

                    'income' => 0,

                    'expense' => 0,

                    'profit' => 0,

                    'margin' => 0,

                ];

            }

            $establishmentSummary[$name]['expense']
                += $expenseItem->amount;

        }

        foreach (
            $establishmentSummary
            as
            &$row
        ) {

            $row['profit']
                =
                $row['income']
                -
                $row['expense'];

            $row['margin']
                =
                $row['income'] > 0

                ? round(
                    (
                        $row['profit']
                        * 100
                    )
                    /
                    $row['income'],
                    2
                )

                : 0;

        }

        unset($row);

        /*
        |--------------------------------------------------------------------------
        | Movimientos financieros
        |--------------------------------------------------------------------------
        */

        $rows = [];

        foreach ($payments as $payment) {

            $rows[] = [

                'date' =>
                    Carbon::parse(
                        $payment->payment_date
                    )->format('d/m/Y'),

                'type' =>
                    '<span class="badge bg-success">Ingreso</span>',

                'concept' =>
                    'Pago consulta #' .
                    $payment->consultation_id,

                'establishment' =>
                    optional(
                        $payment->consultation
                            ->establishment
                    )->name,

                'client' =>
                    optional(
                        $payment->consultation
                            ->client
                    )->full_name,

                'amount' =>
                    number_format(
                        $payment->amount,
                        2
                    ),

            ];

        }

        foreach ($expenses as $expenseItem) {

            $rows[] = [

                'date' =>
                    Carbon::parse(
                        $expenseItem->expense_date
                    )->format('d/m/Y'),

                'type' =>
                    '<span class="badge bg-danger">Gasto</span>',

                'concept' =>
                    $expenseItem->description
                    ?: (
                        config(
                            'options.expense_categories'
                        )[
                            $expenseItem->category
                        ]
                        ??
                        $expenseItem->category
                    ),

                'establishment' =>
                    optional(
                        $expenseItem->establishment
                    )->name,

                'client' => optional(
                    optional(
                        $expenseItem->case
                    )->client
                )->full_name ?? '-',

                'amount' =>
                    number_format(
                        $expenseItem->amount,
                        2
                    ),

            ];

        }

        /*
        |--------------------------------------------------------------------------
        | Ordenar por fecha descendente
        |--------------------------------------------------------------------------
        */

        usort(

            $rows,

            function (
                $a,
                $b
            ) {

                return strtotime(
                    Carbon::createFromFormat(
                        'd/m/Y',
                        $b['date']
                    )->format(
                        'Y-m-d'
                    )
                )

                <=>

                strtotime(
                    Carbon::createFromFormat(
                        'd/m/Y',
                        $a['date']
                    )->format(
                        'Y-m-d'
                    )
                );

            }

        );

        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        return response()->json([

            'summary' => [

                'income' =>
                    round(
                        $income,
                        2
                    ),

                'expense' =>
                    round(
                        $expense,
                        2
                    ),

                'profit' =>
                    round(
                        $profit,
                        2
                    ),

                'net_flow' =>
                    round(
                        $netFlow,
                        2
                    ),

                'margin' =>
                    $margin,

                'avg_ticket' =>
                    $avgTicket,

                'roi' =>
                    $roi,

            ],

            'charts' =>
                $charts,

            'establishment_summary' =>
                array_values(
                    $establishmentSummary
                ),

            'data' =>
                $rows,

        ]);
    }
}