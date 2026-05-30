<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\CaseFile;
use App\Models\CaseActivity;
use App\Models\AgendaEvent;
use App\Models\Establishment;
use App\Models\LegalSpecialty;
use App\Models\User;
use App\Models\NotificationSetting;

use Carbon\Carbon;

class LawyerReportController extends Controller
{
    public function index()
    {
        $establishments = Establishment::orderBy('name')->get();

        $specialties = LegalSpecialty::orderBy('name')->get();

        $lawyers = User::role('Abogado')->orderBy('name')->get();

        return view(
            'reports.lawyers.index',
            compact(
                'establishments',
                'specialties',
                'lawyers'
            )
        );
    }

    public function datatable(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Casos
        |--------------------------------------------------------------------------
        */

        $cases = CaseFile::query()

            ->with([

                'lawyer',

                'client',

                'specialty',

                'activities',

                'agendaEvents',

                'expenses',

                'consultation.payments',

            ]);

        /*
        |--------------------------------------------------------------------------
        | Fecha
        |--------------------------------------------------------------------------
        */

        if ($request->date_from) {

            $cases->whereDate(
                'opened_at',
                '>=',
                $request->date_from
            );

        }

        if ($request->date_to) {

            $cases->whereDate(
                'opened_at',
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

            $cases->where(
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

            $cases->where(
                'legal_specialty_id',
                $request->specialty_id
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Abogado
        |--------------------------------------------------------------------------
        */

        if ($request->lawyer_id) {

            $cases->where(
                'lawyer_id',
                $request->lawyer_id
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Estado
        |--------------------------------------------------------------------------
        */

        if ($request->status) {

            $cases->where(
                'status',
                $request->status
            );

        }

        $cases = $cases->get();

        /*
        |--------------------------------------------------------------------------
        | Configuración
        |--------------------------------------------------------------------------
        */

        $inactiveDays = (int)

            NotificationSetting::get(
                'client_inactivity_days',
                15
            );

        /*
        |--------------------------------------------------------------------------
        | Dataset por abogado
        |--------------------------------------------------------------------------
        */

        $lawyers = [];

        foreach ($cases as $case) {

            $lawyerId =
                $case->lawyer_id;

            $lawyerName =
                optional(
                    $case->lawyer
                )->name
                ??
                'Sin abogado';

            if (!isset(
                $lawyers[$lawyerId]
            )) {

                $lawyers[$lawyerId] = [

                    'lawyer_id' =>

                        $lawyerId,

                    'lawyer_name' =>

                        $lawyerName,

                    'cases' => 0,

                    'active_cases' => 0,

                    'closed_cases' => 0,

                    'activities' => 0,

                    'events' => 0,

                    'income' => 0,

                    'expense' => 0,

                    'profit' => 0,

                    'inactive_cases' => 0,

                ];

            }

            /*
            |--------------------------------------------------------------------------
            | Casos
            |--------------------------------------------------------------------------
            */

            $lawyers[$lawyerId]['cases']++;

            if (

                in_array(

                    $case->status,

                    [

                        'open',

                        'in_progress'

                    ]

                )

            ) {

                $lawyers[$lawyerId]['active_cases']++;

            }

            if (

                $case->status ===
                'closed'

            ) {

                $lawyers[$lawyerId]['closed_cases']++;

            }

            /*
            |--------------------------------------------------------------------------
            | Actividades
            |--------------------------------------------------------------------------
            */

            $activitiesCount =
                $case->activities
                    ->count();

            $lawyers[$lawyerId]['activities']
                +=
                $activitiesCount;

            /*
            |--------------------------------------------------------------------------
            | Eventos
            |--------------------------------------------------------------------------
            */

            $eventsCount =
                $case->agendaEvents
                    ->count();

            $lawyers[$lawyerId]['events']
                +=
                $eventsCount;

            /*
            |--------------------------------------------------------------------------
            | Ingresos
            |--------------------------------------------------------------------------
            */

            $income =

                optional(
                    $case->consultation
                )

                ?

                $case->consultation
                    ->payments
                    ->sum('amount')

                :

                0;

            $lawyers[$lawyerId]['income']
                +=
                $income;

            /*
            |--------------------------------------------------------------------------
            | Gastos
            |--------------------------------------------------------------------------
            */

            $expense =

                $case->expenses
                    ->sum('amount');

            $lawyers[$lawyerId]['expense']
                +=
                $expense;

            /*
            |--------------------------------------------------------------------------
            | Comunicación reciente
            |--------------------------------------------------------------------------
            */

            $lastCommunication =

                $case->activities

                    ->where(
                        'type',
                        'communication'
                    )

                    ->sortByDesc(
                        'activity_at'
                    )

                    ->first();

            if (

                $case->status ===
                'in_progress'

            ) {

                if (

                    !$lastCommunication

                ) {

                    $days =

                        Carbon::parse(
                            $case->opened_at
                        )

                        ->diffInDays(
                            now()
                        );

                    if (

                        $days >=
                        $inactiveDays

                    ) {

                        $lawyers[$lawyerId]['inactive_cases']++;

                    }

                } else {

                    $days =

                        Carbon::parse(
                            $lastCommunication->activity_at
                        )

                        ->diffInDays(
                            now()
                        );

                    if (

                        $days >=
                        $inactiveDays

                    ) {

                        $lawyers[$lawyerId]['inactive_cases']++;

                    }

                }

            }

        }

        /*
        |--------------------------------------------------------------------------
        | Calcular utilidad y margen
        |--------------------------------------------------------------------------
        */

        foreach (

            $lawyers
            as
            &$lawyer

        ) {

            $lawyer['profit'] =

                $lawyer['income']
                -
                $lawyer['expense'];

            $lawyer['margin'] =

                $lawyer['income'] > 0

                ?

                round(

                    (
                        $lawyer['profit']
                        *
                        100
                    )

                    /

                    $lawyer['income'],

                    2

                )

                :

                0;

            $lawyer['avg_activities'] =

                $lawyer['cases'] > 0

                ?

                round(

                    $lawyer['activities']
                    /
                    $lawyer['cases'],

                    2

                )

                :

                0;

        }

        unset($lawyer);

        /*
        |--------------------------------------------------------------------------
        | KPIs generales
        |--------------------------------------------------------------------------
        */

        $dataset =
            collect(
                $lawyers
            );

        $totalLawyers =
            $dataset->count();

        $totalCases =
            $dataset->sum(
                'cases'
            );

        $totalActiveCases =
            $dataset->sum(
                'active_cases'
            );

        $totalClosedCases =
            $dataset->sum(
                'closed_cases'
            );

        $totalActivities =
            $dataset->sum(
                'activities'
            );

        $totalEvents =
            $dataset->sum(
                'events'
            );

        $totalIncome =
            $dataset->sum(
                'income'
            );

        $totalProfit =
            $dataset->sum(
                'profit'
            );

        /*
        |--------------------------------------------------------------------------
        | Abogado más rentable
        |--------------------------------------------------------------------------
        */

        $bestLawyer =

            $dataset

                ->sortByDesc(
                    'profit'
                )

                ->first();

        /*
        |--------------------------------------------------------------------------
        | Casos sin comunicación reciente
        |--------------------------------------------------------------------------
        */

        $inactiveCases =

            $dataset->sum(
                'inactive_cases'
            );

        /*
        |--------------------------------------------------------------------------
        | Promedio general actividades/caso
        |--------------------------------------------------------------------------
        */

        $avgActivitiesPerCase =

            $totalCases > 0

            ?

            round(

                $totalActivities
                /
                $totalCases,

                2

            )

            :

            0;

        /*
        |--------------------------------------------------------------------------
        | Ranking ingresos
        |--------------------------------------------------------------------------
        */

        $rankingIncome =

            $dataset

                ->sortByDesc(
                    'income'
                )

                ->values();

        /*
        |--------------------------------------------------------------------------
        | Ranking utilidad
        |--------------------------------------------------------------------------
        */

        $rankingProfit =

            $dataset

                ->sortByDesc(
                    'profit'
                )

                ->values();

        /*
        |--------------------------------------------------------------------------
        | Casos por abogado
        |--------------------------------------------------------------------------
        */

        $casesByLawyer = [];

        foreach (

            $dataset
            as
            $lawyer

        ) {

            $casesByLawyer[
                $lawyer['lawyer_name']
            ] =

                $lawyer['cases'];

        }

        /*
        |--------------------------------------------------------------------------
        | Actividades por abogado
        |--------------------------------------------------------------------------
        */

        $activitiesByLawyer = [];

        foreach (

            $dataset
            as
            $lawyer

        ) {

            $activitiesByLawyer[
                $lawyer['lawyer_name']
            ] =

                $lawyer['activities'];

        }

        /*
        |--------------------------------------------------------------------------
        | Casos por estado
        |--------------------------------------------------------------------------
        */

        $statusTotals = [

            'Activos' => 0,

            'Cerrados' => 0,

            'Otros' => 0,

        ];

        foreach (

            $cases
            as
            $case

        ) {

            if (

                in_array(

                    $case->status,

                    [

                        'open',

                        'in_progress'

                    ]

                )

            ) {

                $statusTotals[
                    'Activos'
                ]++;

            }

            elseif (

                $case->status ===
                'closed'

            ) {

                $statusTotals[
                    'Cerrados'
                ]++;

            }

            else {

                $statusTotals[
                    'Otros'
                ]++;

            }

        }

        /*
        |--------------------------------------------------------------------------
        | Charts
        |--------------------------------------------------------------------------
        */

        $charts = [

            /*
            |--------------------------------------------------------------------------
            | Ranking ingresos
            |--------------------------------------------------------------------------
            */

            'income' => [

                'labels' =>

                    $rankingIncome

                        ->pluck(
                            'lawyer_name'
                        )

                        ->values(),

                'values' =>

                    $rankingIncome

                        ->pluck(
                            'income'
                        )

                        ->values(),

            ],

            /*
            |--------------------------------------------------------------------------
            | Ranking utilidad
            |--------------------------------------------------------------------------
            */

            'profit' => [

                'labels' =>

                    $rankingProfit

                        ->pluck(
                            'lawyer_name'
                        )

                        ->values(),

                'values' =>

                    $rankingProfit

                        ->pluck(
                            'profit'
                        )

                        ->values(),

            ],

            /*
            |--------------------------------------------------------------------------
            | Casos por abogado
            |--------------------------------------------------------------------------
            */

            'cases' => [

                'labels' =>

                    array_keys(
                        $casesByLawyer
                    ),

                'values' =>

                    array_values(
                        $casesByLawyer
                    ),

            ],

            /*
            |--------------------------------------------------------------------------
            | Actividades por abogado
            |--------------------------------------------------------------------------
            */

            'activities' => [

                'labels' =>

                    array_keys(
                        $activitiesByLawyer
                    ),

                'values' =>

                    array_values(
                        $activitiesByLawyer
                    ),

            ],

            /*
            |--------------------------------------------------------------------------
            | Casos por estado
            |--------------------------------------------------------------------------
            */

            'status' => [

                'labels' =>

                    array_keys(
                        $statusTotals
                    ),

                'values' =>

                    array_values(
                        $statusTotals
                    ),

            ],

        ];

        /*
        |--------------------------------------------------------------------------
        | Tabla principal
        |--------------------------------------------------------------------------
        */

        $rows = $dataset

            ->sortByDesc(
                'profit'
            )

            ->values()

            ->map(function ($lawyer) {

                return [

                    'lawyer_name' =>

                        $lawyer['lawyer_name'],

                    'cases' =>

                        $lawyer['cases'],

                    'active_cases' =>

                        $lawyer['active_cases'],

                    'closed_cases' =>

                        $lawyer['closed_cases'],

                    'activities' =>

                        $lawyer['activities'],

                    'events' =>

                        $lawyer['events'],

                    'inactive_cases' =>

                        $lawyer['inactive_cases'],

                    'avg_activities' =>

                        number_format(
                            $lawyer['avg_activities'],
                            2
                        ),

                    'income' =>

                        number_format(
                            $lawyer['income'],
                            2
                        ),

                    'expense' =>

                        number_format(
                            $lawyer['expense'],
                            2
                        ),

                    'profit' =>

                        number_format(
                            $lawyer['profit'],
                            2
                        ),

                    'margin' =>

                        number_format(
                            $lawyer['margin'],
                            2
                        ) . '%',

                    /*
                    |--------------------------------------------------------------------------
                    | Valores crudos
                    |--------------------------------------------------------------------------
                    */

                    'income_raw' =>

                        $lawyer['income'],

                    'expense_raw' =>

                        $lawyer['expense'],

                    'profit_raw' =>

                        $lawyer['profit'],

                    'margin_raw' =>

                        $lawyer['margin'],

                ];

            });

        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        return response()->json([

            'summary' => [

                'total_lawyers' =>

                    $totalLawyers,

                'total_cases' =>

                    $totalCases,

                'active_cases' =>

                    $totalActiveCases,

                'closed_cases' =>

                    $totalClosedCases,

                'activities' =>

                    $totalActivities,

                'events' =>

                    $totalEvents,

                'income' =>

                    round(
                        $totalIncome,
                        2
                    ),

                'profit' =>

                    round(
                        $totalProfit,
                        2
                    ),

                'inactive_cases' =>

                    $inactiveCases,

                'avg_activities' =>

                    $avgActivitiesPerCase,

                'best_lawyer_name' =>

                    $bestLawyer['lawyer_name']
                    ??
                    '-',

                'best_lawyer_profit' =>

                    round(

                        $bestLawyer['profit']
                        ??
                        0,

                        2

                    ),

            ],

            'charts' =>

                $charts,

            'data' =>

                $rows,

        ]);

    }

}