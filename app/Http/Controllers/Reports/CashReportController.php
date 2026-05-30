<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\Establishment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CashReportController extends Controller
{
    public function index()
    {
        $establishments = Establishment::orderBy('name')
            ->get();

        return view(
            'reports.cash.index',
            compact(
                'establishments'
            )
        );
    }

    public function datatable(Request $request)
    {
        $from = $request->filled('date_start')
            ? Carbon::parse($request->date_start)->startOfDay()
            : now()->startOfMonth();

        $to = $request->filled('date_end')
            ? Carbon::parse($request->date_end)->endOfDay()
            : now()->endOfDay();

        $establishmentId =
            $request->establishment_id;

        /*
        |--------------------------------------------------------------------------
        | PAYMENTS
        |--------------------------------------------------------------------------
        */

        $payments = Payment::query()

            ->with([
                'consultation.case',
                'installment'
            ])

            ->whereBetween(
                'payment_date',
                [$from, $to]
            );

        /*
        |--------------------------------------------------------------------------
        | EXPENSES
        |--------------------------------------------------------------------------
        */

        $expenses = Expense::query()

            ->with([
                'case'
            ])

            ->whereBetween(
                'expense_date',
                [$from, $to]
            );

        /*
        |--------------------------------------------------------------------------
        | FILTRO SEDE
        |--------------------------------------------------------------------------
        */

        if($establishmentId)
        {
            $payments->whereHas(
                'consultation',
                function($q) use ($establishmentId){

                    $q->where(
                        'establishment_id',
                        $establishmentId
                    );

                }
            );

            $expenses->where(
                'establishment_id',
                $establishmentId
            );
        }

        /*
        |--------------------------------------------------------------------------
        | KPI
        |--------------------------------------------------------------------------
        */

        $totalIncome =
            (clone $payments)
                ->sum('amount');

        $totalExpense =
            (clone $expenses)
                ->sum('amount');

        $netCash =
            $totalIncome
            -
            $totalExpense;

        $paymentsCount =
            (clone $payments)
                ->count();

        /*
        |--------------------------------------------------------------------------
        | METODOS DE PAGO
        |--------------------------------------------------------------------------
        */

        $methods = [];

        foreach(
            config('options.payment_methods')
            as $key => $label
        )
        {
            $income =

                (clone $payments)

                    ->where(
                        'payment_method',
                        $key
                    )

                    ->sum('amount');

            $expense =

                (clone $expenses)

                    ->where(
                        'payment_method',
                        $key
                    )

                    ->sum('amount');

            $methods[] = [

                'method' => $label,

                'income' => $income,

                'expense' => $expense,

                'net' => $income - $expense,

            ];
        }

        /*
        |--------------------------------------------------------------------------
        | MOVIMIENTOS
        |--------------------------------------------------------------------------
        */

        $paymentRows =

            (clone $payments)

                ->get()

                ->map(function($payment){

                    $caseId =
                        optional(
                            $payment
                                ->consultation
                                ?->case
                        )->id;

                    return [

                        'date' =>
                            optional(
                                $payment->payment_date
                            )->format('d/m/Y'),

                        'type' =>
                            'Ingreso',

                        'payment_method' =>
                            config(
                                'options.payment_methods'
                            )[
                                $payment->payment_method
                            ]
                            ??
                            $payment->payment_method,

                        'concept' => $payment->description,

                        'amount' =>
                            number_format(
                                $payment->amount,
                                2
                            ),

                    ];
                });

        $expenseRows =

            (clone $expenses)

                ->get()

                ->map(function($expense){

                    return [

                        'date' =>
                            optional(
                                $expense->expense_date
                            )->format('d/m/Y'),

                        'type' =>
                            'Gasto',

                        'payment_method' =>
                            config(
                                'options.payment_methods'
                            )[
                                $expense->payment_method
                            ]
                            ??
                            $expense->payment_method,

                        'concept' =>

                            'Caso '

                            .

                            $expense->case_id

                            .

                            ' - '

                            .

                            $expense->description,

                        'amount' =>
                            number_format(
                                $expense->amount,
                                2
                            ),

                    ];
                });

        $movements =

            $paymentRows

                ->concat(
                    $expenseRows
                )

                ->sortByDesc('date')

                ->values();

        $chart = [

            'categories' =>

                collect($methods)

                    ->pluck('method')

                    ->values(),

            'income' =>

                collect($methods)

                    ->pluck('income')

                    ->values(),

            'expense' =>

                collect($methods)

                    ->pluck('expense')

                    ->values(),

        ];

        return response()->json([

            'kpis' => [

                'income' =>
                    round(
                        $totalIncome,
                        2
                    ),

                'expense' =>
                    round(
                        $totalExpense,
                        2
                    ),

                'net' =>
                    round(
                        $netCash,
                        2
                    ),

                'payments_count' =>
                    $paymentsCount,

            ],

            'methods' =>
                $methods,

            'chart' =>
                $chart,

            'rows' =>
                $movements,

        ]);
    }
}