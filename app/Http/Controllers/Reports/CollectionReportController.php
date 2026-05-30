<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ConsultationInstallment;
use App\Models\Establishment;
use App\Models\User;
use Carbon\Carbon;

class CollectionReportController extends Controller
{
    public function index()
    {
        $establishments = Establishment::query()
            ->orderBy('name')
            ->get();

        $lawyers = User::role('Abogado')
            ->orderBy('name')
            ->get();

        return view(
            'reports.collection.index',
            compact(
                'establishments',
                'lawyers'
            )
        );
    }

    public function datatable(Request $request)
    {
        $query = ConsultationInstallment::query()

            ->with([

                'consultation.client',
                'consultation.lawyer',
                'consultation.establishment',

            ]);

        /*
        |--------------------------------------------------------------------------
        | Sede
        |--------------------------------------------------------------------------
        */

        if ($request->establishment_id) {

            $query->whereHas(

                'consultation',

                function ($q) use ($request) {

                    $q->where(
                        'establishment_id',
                        $request->establishment_id
                    );

                }

            );

        }

        /*
        |--------------------------------------------------------------------------
        | Abogado
        |--------------------------------------------------------------------------
        */

        if ($request->lawyer_id) {

            $query->whereHas(

                'consultation',

                function ($q) use ($request) {

                    $q->where(
                        'lawyer_id',
                        $request->lawyer_id
                    );

                }

            );

        }

        /*
        |--------------------------------------------------------------------------
        | Incluir vencidas anteriores
        |--------------------------------------------------------------------------
        */

        if ($request->include_overdue != 1) {

            /*
            |--------------------------------------------------------------------------
            | SOLO rango seleccionado
            |--------------------------------------------------------------------------
            */

            if ($request->date_from) {

                $query->whereDate(
                    'due_date',
                    '>=',
                    $request->date_from
                );

            }

            if ($request->date_to) {

                $query->whereDate(
                    'due_date',
                    '<=',
                    $request->date_to
                );

            }

        } else {

            /*
            |--------------------------------------------------------------------------
            | Agregar vencidas anteriores
            |--------------------------------------------------------------------------
            */

            $query->where(function ($q) use ($request) {

                /*
                |--------------------------------------------------------------------------
                | Rango actual
                |--------------------------------------------------------------------------
                */

                if ($request->date_from) {

                    $q->whereDate(
                        'due_date',
                        '>=',
                        $request->date_from
                    );

                }

                if ($request->date_to) {

                    $q->whereDate(
                        'due_date',
                        '<=',
                        $request->date_to
                    );

                }

                /*
                |--------------------------------------------------------------------------
                | O vencidas anteriores
                |--------------------------------------------------------------------------
                */

                $q->orWhere(function ($sub) {

                    $sub

                        ->whereDate(
                            'due_date',
                            '<',
                            now()
                        )

                        ->whereRaw(
                            'amount > paid_amount'
                        );

                });

            });

        }

        $installments = $query
            ->orderBy('due_date')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Estado
        |--------------------------------------------------------------------------
        */

        if ($request->status) {

            $installments = $installments->filter(

                function ($item) use ($request) {

                    $pending =
                        $item->amount
                        -
                        $item->paid_amount;

                    if (
                        $request->status == 'paid'
                    ) {

                        return $pending <= 0;

                    }

                    if (
                        $request->status == 'pending'
                    ) {

                        return
                            $pending > 0
                            &&
                            $item->due_date >= now();

                    }

                    if (
                        $request->status == 'overdue'
                    ) {

                        return
                            $pending > 0
                            &&
                            $item->due_date < now();

                    }

                    return true;

                }

            );

        }

        /*
        |--------------------------------------------------------------------------
        | KPIs
        |--------------------------------------------------------------------------
        */

        $totalCollected =
            $installments->sum('paid_amount');

        $totalPending =
            $installments->sum(

                function ($item) {

                    return
                        $item->amount
                        -
                        $item->paid_amount;

                }

            );

        $totalOverdue =
            $installments

                ->filter(

                    function ($item) {

                        return

                            (
                                $item->amount
                                >
                                $item->paid_amount
                            )

                            &&

                            (
                                $item->due_date
                                <
                                now()
                            );

                    }

                )

                ->sum(

                    function ($item) {

                        return
                            $item->amount
                            -
                            $item->paid_amount;

                    }

                );

        $moroseClients =
            $installments

                ->filter(

                    function ($item) {

                        return

                            (
                                $item->amount
                                >
                                $item->paid_amount
                            )

                            &&

                            (
                                $item->due_date
                                <
                                now()
                            );

                    }

                )

                ->groupBy(
                    'consultation.client_id'
                )

                ->count();

        /*
        |--------------------------------------------------------------------------
        | KPI Efectividad Cobranza
        |--------------------------------------------------------------------------
        */

        $totalAmount =
            $installments->sum('amount');

        $collectionEffectiveness =
            $totalAmount > 0

                ? round(
                    ($totalCollected * 100)
                    / $totalAmount,
                    2
                )

                : 0;

        /*
        |--------------------------------------------------------------------------
        | KPI Promedio atraso
        |--------------------------------------------------------------------------
        */

        $avgDaysLate =

            $installments

                ->filter(function ($item) {

                    return

                        $item->amount
                        >
                        $item->paid_amount

                        &&

                        $item->due_date < now();

                })

                ->map(function ($item) {

                    return Carbon::parse(
                        $item->due_date
                    )->diffInDays(now());

                })

                ->avg();

        $avgDaysLate =
            round($avgDaysLate ?? 0);

        /*
        |--------------------------------------------------------------------------
        | Charts
        |--------------------------------------------------------------------------
        */

        $chartStatus = [

            'paid' => 0,
            'pending' => 0,
            'overdue' => 0,

        ];

        foreach ($installments as $item) {

            $pending =
                $item->amount
                -
                $item->paid_amount;

            if ($pending <= 0) {

                $chartStatus['paid']++;

            } elseif ($item->due_date < now()) {

                $chartStatus['overdue']++;

            } else {

                $chartStatus['pending']++;

            }

        }

        /*
        |--------------------------------------------------------------------------
        | Ingresos por abogado
        |--------------------------------------------------------------------------
        */

        $lawyerChart = [];

        foreach (

            $installments
                ->groupBy(
                    'consultation.lawyer.name'
                )

            as $lawyer => $items

        ) {

            $lawyerChart[] = [

                'lawyer' => $lawyer ?? 'Sin abogado',

                'total' => $items->sum(
                    'paid_amount'
                )

            ];

        }

        /*
        |--------------------------------------------------------------------------
        | Cobranza por sede
        |--------------------------------------------------------------------------
        */

        $establishmentChart = [];

        foreach (

            $installments
                ->groupBy(
                    'consultation.establishment.name'
                )

            as $establishment => $items

        ) {

            $establishmentChart[] = [

                'establishment' =>
                    $establishment ?? 'Sin sede',

                'total' =>
                    $items->sum(
                        'paid_amount'
                    )

            ];

        }

        /*
        |--------------------------------------------------------------------------
        | Evolución mensual
        |--------------------------------------------------------------------------
        */

        $monthlyChart = [];

        foreach (

            $installments
                ->groupBy(function ($item) {

                    return Carbon::parse(
                        $item->due_date
                    )->format('Y-m');

                })

            as $month => $items

        ) {

            $monthlyChart[] = [

                'month' => $month,

                'total' =>
                    $items->sum(
                        'paid_amount'
                    )

            ];

        }

        /*
        |--------------------------------------------------------------------------
        | Antigüedad deuda
        |--------------------------------------------------------------------------
        */

        $agingChart = [

            '1_7' => 0,

            '8_30' => 0,

            '31_60' => 0,

            '60_plus' => 0,

        ];

        foreach ($installments as $item) {

            $pending =
                $item->amount
                -
                $item->paid_amount;

            if (

                $pending <= 0

                ||

                $item->due_date >= now()

            ) {

                continue;

            }

            $days = Carbon::parse(
                $item->due_date
            )->diffInDays(now());

            if ($days <= 7) {

                $agingChart['1_7'] += $pending;

            } elseif ($days <= 30) {

                $agingChart['8_30'] += $pending;

            } elseif ($days <= 60) {

                $agingChart['31_60'] += $pending;

            } else {

                $agingChart['60_plus'] += $pending;

            }

        }

        /*
        |--------------------------------------------------------------------------
        | Top clientes morosos
        |--------------------------------------------------------------------------
        */

        $topClients =

            $installments

                ->groupBy(
                    'consultation.client.full_name'
                )

                ->map(function ($items, $client) {

                    $debt =

                        $items->sum(function ($item) {

                            return

                                $item->amount

                                -

                                $item->paid_amount;

                        });

                    return [

                        'client' => $client,

                        'debt' => round(
                            $debt,
                            2
                        ),

                    ];

                })

                ->sortByDesc('debt')

                ->take(10)

                ->values();

        /*
        |--------------------------------------------------------------------------
        | Ranking abogados
        |--------------------------------------------------------------------------
        */

        $lawyerRanking =

            $installments

                ->groupBy(
                    'consultation.lawyer.name'
                )

                ->map(function ($items, $lawyer) {

                    return [

                        'lawyer' => $lawyer,

                        'collected' =>
                            round(
                                $items->sum(
                                    'paid_amount'
                                ),
                                2
                            ),

                        'pending' =>
                            round(

                                $items->sum(function ($item) {

                                    return

                                        $item->amount

                                        -

                                        $item->paid_amount;

                                }),

                                2

                            )

                    ];

                })

                ->sortByDesc('collected')

                ->values();

        /*
        |--------------------------------------------------------------------------
        | DataTable
        |--------------------------------------------------------------------------
        */

        $data = $installments->map(

            function ($item) {

                $pending =
                    $item->amount
                    -
                    $item->paid_amount;

                /*
                |--------------------------------------------------------------------------
                | Estado
                |--------------------------------------------------------------------------
                */

                if ($pending <= 0) {

                    $status =
                        '<span class="badge bg-success">
                            Pagado
                        </span>';

                    $statusRaw = 'paid';

                } elseif (
                    $item->due_date < now()
                ) {

                    $status =
                        '<span class="badge bg-danger">
                            Vencido
                        </span>';

                    $statusRaw = 'overdue';

                } else {

                    $status =
                        '<span class="badge bg-warning text-dark">
                            Pendiente
                        </span>';

                    $statusRaw = 'pending';

                }

                /*
                |--------------------------------------------------------------------------
                | Días atraso
                |--------------------------------------------------------------------------
                */

                $daysLate = 0;

                if (

                    $pending > 0

                    &&

                    $item->due_date < now()

                ) {

                    $daysLate =
                        Carbon::parse(
                            $item->due_date
                        )->diffInDays(now());

                }

                return [

                    'client' =>
                        $item->consultation->client->full_name
                        ?? '-',

                    'consultation' =>
                        $item->consultation->title
                        ?? '-',

                    'lawyer' =>
                        $item->consultation->lawyer->name
                        ?? '-',

                    'establishment' =>
                        $item->consultation
                            ->establishment
                            ->name
                        ?? '-',

                    'installment' =>
                        'Cuota #'.$item->installment_number,

                    'due_date' =>
                        optional($item->due_date)
                            ?->format('d/m/Y'),

                    'days_late' =>
                        $daysLate,

                    'amount' =>
                        'S/ '.number_format(
                            $item->amount,
                            2
                        ),

                    'paid' =>
                        'S/ '.number_format(
                            $item->paid_amount,
                            2
                        ),

                    'pending' =>
                        'S/ '.number_format(
                            $pending,
                            2
                        ),

                    'status' =>
                        $status,

                    'status_raw' =>
                        $statusRaw,

                ];

            }

        );

        return response()->json([

            'data' => $data->values(),

            'summary' => [

                'collected' =>
                    number_format(
                        $totalCollected,
                        2
                    ),

                'pending' =>
                    number_format(
                        $totalPending,
                        2
                    ),

                'overdue' =>
                    number_format(
                        $totalOverdue,
                        2
                    ),

                'installments' =>
                    $installments->count(),

                'morose_clients' =>
                    $moroseClients,

                'effectiveness' =>
                    $collectionEffectiveness,

                'avg_days_late' =>
                    $avgDaysLate,

            ],

            'charts' => [

                'status' => $chartStatus,

                'lawyers' => $lawyerChart,

                'establishments' =>
                    $establishmentChart,

                'monthly' =>
                    $monthlyChart,

                'aging' =>
                    $agingChart,

            ],
            'top_clients' =>
                $topClients,

            'lawyer_ranking' =>
                $lawyerRanking,

        ]);
    }
}