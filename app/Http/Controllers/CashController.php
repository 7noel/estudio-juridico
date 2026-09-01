<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Establishment;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CashController extends Controller
{
    /**
     * Mostrar la página de Caja (caja chica).
     */
    public function index()
    {
        $establishments = Establishment::orderBy('name')->get();

        $userEstablishmentId =
            auth()->user()->employee?->establishment_id
            ?? auth()->user()->establishment_id;

        return view('cash.index', compact('establishments', 'userEstablishmentId'));
    }

    /**
     * Movimientos de caja (ingresos + gastos) para el DataTable.
     */
    public function datatable(Request $request)
    {
        $from = $request->filled('date_start')
            ? Carbon::parse($request->date_start)->startOfDay()
            : now()->startOfMonth();

        $to = $request->filled('date_end')
            ? Carbon::parse($request->date_end)->endOfDay()
            : now()->endOfDay();

        $establishmentId = $request->establishment_id;

        /*
        |--------------------------------------------------------------------------
        | INGRESOS (payments: cuotas + ingresos generales)
        |--------------------------------------------------------------------------
        */

        $payments = Payment::query()
            ->with(['consultation.case', 'installment'])
            ->whereBetween('payment_date', [$from, $to]);

        /*
        |--------------------------------------------------------------------------
        | GASTOS (expenses: por caso + gastos generales)
        |--------------------------------------------------------------------------
        */

        $expenses = Expense::query()
            ->with('case')
            ->whereBetween('expense_date', [$from, $to]);

        /*
        |--------------------------------------------------------------------------
        | FILTRO SEDE
        |--------------------------------------------------------------------------
        */

        if ($establishmentId) {
            $payments->where('establishment_id', $establishmentId);
            $expenses->where('establishment_id', $establishmentId);
        }

        /*
        |--------------------------------------------------------------------------
        | KPIs
        |--------------------------------------------------------------------------
        */

        $totalIncome = (clone $payments)->sum('amount');
        $totalExpense = (clone $expenses)->sum('amount');
        $netCash = $totalIncome - $totalExpense;

        /*
        |--------------------------------------------------------------------------
        | FILAS DE MOVIMIENTOS
        |--------------------------------------------------------------------------
        */

        $paymentRows = (clone $payments)
            ->get()
            ->map(function ($payment) {
                $caseId = $payment->consultation?->case?->id;

                $origin = $caseId
                    ? 'Caso ' . $caseId . ' / Cuota'
                    : ($payment->consultation_id
                        ? 'Consulta ' . $payment->consultation_id . ' / Cuota'
                        : 'General');

                return [
                    'id' => $payment->id,
                    'sort_date' => optional($payment->payment_date)->format('Y-m-d'),
                    'date' => optional($payment->payment_date)->format('d/m/Y'),
                    'type' => 'Ingreso',
                    'kind' => 'income',
                    'origin' => $origin,
                    'payment_method' => config('options.payment_methods')[$payment->payment_method] ?? $payment->payment_method,
                    'concept' => $payment->description,
                    'reference' => $payment->reference,
                    'amount' => number_format($payment->amount, 2),
                    'deletable' => $payment->consultation_id === null,
                ];
            });

        $expenseRows = (clone $expenses)
            ->get()
            ->map(function ($expense) {
                $concept = $expense->description;

                if ($expense->case_id) {
                    $concept = 'Caso ' . $expense->case_id . ' - ' . ($expense->description ?? '');
                }

                return [
                    'id' => $expense->id,
                    'sort_date' => optional($expense->expense_date)->format('Y-m-d'),
                    'date' => optional($expense->expense_date)->format('d/m/Y'),
                    'type' => 'Gasto',
                    'kind' => 'expense',
                    'origin' => $expense->case_id ? 'Caso ' . $expense->case_id : 'General',
                    'payment_method' => config('options.payment_methods')[$expense->payment_method] ?? $expense->payment_method,
                    'concept' => $concept,
                    'reference' => $expense->reference,
                    'amount' => number_format($expense->amount, 2),
                    'deletable' => $expense->case_id === null,
                ];
            });

        $movements = $paymentRows
            ->concat($expenseRows)
            ->sortByDesc('sort_date')
            ->values()
            ->map(function ($row) {
                unset($row['sort_date']);
                return $row;
            });

        return response()->json([
            'kpis' => [
                'income' => round($totalIncome, 2),
                'expense' => round($totalExpense, 2),
                'net' => round($netCash, 2),
                'movements_count' => $movements->count(),
            ],
            'rows' => $movements,
        ]);
    }

    /**
     * Registrar un ingreso de caja no relacionado a una consulta.
     */
    public function incomeStore(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string|in:' . implode(',', array_keys(config('options.payment_methods'))),
            'reference' => 'nullable|string|max:255',
            'description' => 'required|string|max:1000',
        ]);

        $establishmentId =
            auth()->user()->employee?->establishment_id
            ?? auth()->user()->establishment_id;

        Payment::create([
            'establishment_id' => $establishmentId,
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'payment_method' => $request->payment_method,
            'reference' => $request->reference,
            'description' => $request->description,
            'user_id' => auth()->id(),
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Ingreso registrado correctamente.',
        ]);
    }

    /**
     * Eliminar un ingreso general de caja (no vinculado a consulta).
     */
    public function incomeDestroy(Request $request)
    {
        $payment = Payment::findOrFail($request->id);

        if ($payment->consultation_id) {
            return response()->json([
                'error' => 'Este ingreso está vinculado a una consulta y no puede eliminarse desde caja.',
            ], 422);
        }

        $payment->delete();

        return response()->json([
            'ok' => true,
            'message' => 'Ingreso eliminado correctamente.',
        ]);
    }
}
