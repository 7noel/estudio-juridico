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

class OperationalReportController extends Controller
{
    public function index()
    {
        $establishments = Establishment::orderBy('name')->get();

        $specialties = LegalSpecialty::orderBy('name')->get();

        $lawyers = User::orderBy('name')->get();

        return view(
            'reports.operational.index',
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

                'client',

                'lawyer',

                'specialty',

                'activities',

                'agendaEvents',

                'establishment',

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
        | Dataset principal
        |--------------------------------------------------------------------------
        */

        $dataset = [];

        $totalActivities = 0;

        $totalEvents = 0;

        $casesWithoutActivities = 0;

        $casesWithoutFutureEvents = 0;

        $casesWithoutRecentCommunication = 0;

        foreach ($cases as $case) {

            /*
            |--------------------------------------------------------------------------
            | Actividades
            |--------------------------------------------------------------------------
            */

            $activitiesCount =
                $case->activities->count();

            $totalActivities +=
                $activitiesCount;

            if ($activitiesCount == 0) {

                $casesWithoutActivities++;

            }

            /*
            |--------------------------------------------------------------------------
            | Última actividad
            |--------------------------------------------------------------------------
            */

            $lastActivity =
                $case->activities

                    ->sortByDesc(
                        'activity_at'
                    )

                    ->first();

            /*
            |--------------------------------------------------------------------------
            | Comunicación más reciente
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

            if ($case->status === 'in_progress') {

                if (!$lastCommunication) {

                    $days =
                        Carbon::parse(
                            $case->opened_at
                        )->diffInDays(
                            now()
                        );

                    if (
                        $days >=
                        $inactiveDays
                    ) {

                        $casesWithoutRecentCommunication++;

                    }

                } else {

                    $days =
                        Carbon::parse(
                            $lastCommunication->activity_at
                        )->diffInDays(
                            now()
                        );

                    if (
                        $days >=
                        $inactiveDays
                    ) {

                        $casesWithoutRecentCommunication++;

                    }

                }

            }

            /*
            |--------------------------------------------------------------------------
            | Próximo evento
            |--------------------------------------------------------------------------
            */

            $nextEvent =
                $case->agendaEvents

                    ->where(
                        'start_datetime',
                        '>=',
                        now()
                    )

                    ->sortBy(
                        'start_datetime'
                    )

                    ->first();

            if (!$nextEvent) {

                $casesWithoutFutureEvents++;

            }

            $totalEvents +=
                $case->agendaEvents->count();

            /*
            |--------------------------------------------------------------------------
            | Dataset
            |--------------------------------------------------------------------------
            */

            $dataset[] = [

                'case_id' =>
                    $case->id,

                'case_title' =>
                    $case->title,

                'client' =>
                    optional(
                        $case->client
                    )->full_name,

                'specialty' =>
                    optional(
                        $case->specialty
                    )->name,

                'lawyer' =>
                    optional(
                        $case->lawyer
                    )->name,

                'status' =>
                    $case->status,

                'activities_count' =>
                    $activitiesCount,

                'last_activity' =>
                    optional(
                        $lastActivity
                    )->activity_at,

                'next_event' =>
                    optional(
                        $nextEvent
                    )->start_datetime,

            ];

        }

        /*
        |--------------------------------------------------------------------------
        | KPIs
        |--------------------------------------------------------------------------
        */

        $totalCases =
            count($dataset);

        $activeCases =
            collect($dataset)

                ->whereIn(
                    'status',
                    [
                        'open',
                        'in_progress'
                    ]
                )

                ->count();

        $closedCases =
            collect($dataset)

                ->where(
                    'status',
                    'closed'
                )

                ->count();

        $pausedCases =
            collect($dataset)

                ->where(
                    'status',
                    'paused'
                )

                ->count();

        $avgActivitiesPerCase =
            $totalCases > 0

            ? round(
                $totalActivities
                /
                $totalCases,
                2
            )

            : 0;

        /*
        |--------------------------------------------------------------------------
        | Casos por Estado
        |--------------------------------------------------------------------------
        */

        $casesByStatus = [];

        foreach ($dataset as $row) {

            $status = $row['status'];

            if (!isset(
                $casesByStatus[$status]
            )) {

                $casesByStatus[$status] = 0;

            }

            $casesByStatus[$status]++;

        }

        /*
        |--------------------------------------------------------------------------
        | Casos por Especialidad
        |--------------------------------------------------------------------------
        */

        $casesBySpecialty = [];

        foreach ($dataset as $row) {

            $specialty =
                $row['specialty']
                ?: 'Sin especialidad';

            if (!isset(
                $casesBySpecialty[$specialty]
            )) {

                $casesBySpecialty[$specialty] = 0;

            }

            $casesBySpecialty[$specialty]++;

        }

        /*
        |--------------------------------------------------------------------------
        | Casos por Abogado
        |--------------------------------------------------------------------------
        */

        $casesByLawyer = [];

        foreach ($dataset as $row) {

            $lawyer =
                $row['lawyer']
                ?: 'Sin abogado';

            if (!isset(
                $casesByLawyer[$lawyer]
            )) {

                $casesByLawyer[$lawyer] = 0;

            }

            $casesByLawyer[$lawyer]++;

        }

        /*
        |--------------------------------------------------------------------------
        | Actividades por Tipo
        |--------------------------------------------------------------------------
        */

        $activitiesByType = [];

        $activities = CaseActivity::query()
            ->whereIn(
                'case_id',
                $cases->pluck('id')
            );

        if ($request->date_from) {

            $activities->whereDate(
                'activity_at',
                '>=',
                $request->date_from
            );

        }

        if ($request->date_to) {

            $activities->whereDate(
                'activity_at',
                '<=',
                $request->date_to
            );

        }

        $activities = $activities->get();

        foreach ($activities as $activity) {

            $type =
                $activity->type
                ?: 'Sin tipo';

            if (!isset(
                $activitiesByType[$type]
            )) {

                $activitiesByType[$type] = 0;

            }

            $activitiesByType[$type]++;

        }

        /*
        |--------------------------------------------------------------------------
        | Eventos por Tipo
        |--------------------------------------------------------------------------
        */

        $eventsByType = [];

        $events = AgendaEvent::query()
            ->whereIn(
                'case_id',
                $cases->pluck('id')
            );

        if ($request->date_from) {

            $events->whereDate(
                'start_datetime',
                '>=',
                $request->date_from
            );

        }

        if ($request->date_to) {

            $events->whereDate(
                'start_datetime',
                '<=',
                $request->date_to
            );

        }

        $events = $events->get();

        foreach ($events as $event) {

            $type =
                $event->type
                ?: 'Sin tipo';

            if (!isset(
                $eventsByType[$type]
            )) {

                $eventsByType[$type] = 0;

            }

            $eventsByType[$type]++;

        }

        /*
        |--------------------------------------------------------------------------
        | Ordenamientos
        |--------------------------------------------------------------------------
        */

        arsort($casesByLawyer);

        arsort($casesBySpecialty);

        arsort($activitiesByType);

        arsort($eventsByType);

        /*
        |--------------------------------------------------------------------------
        | Charts
        |--------------------------------------------------------------------------
        */

        $charts = [

            /*
            |--------------------------------------------------------------------------
            | Casos por estado
            |--------------------------------------------------------------------------
            */

            'status' => [

                'labels' =>

                    array_keys(
                        $casesByStatus
                    ),

                'values' =>

                    array_values(
                        $casesByStatus
                    ),

            ],

            /*
            |--------------------------------------------------------------------------
            | Casos por especialidad
            |--------------------------------------------------------------------------
            */

            'specialties' => [

                'labels' =>

                    array_keys(
                        $casesBySpecialty
                    ),

                'values' =>

                    array_values(
                        $casesBySpecialty
                    ),

            ],

            /*
            |--------------------------------------------------------------------------
            | Casos por abogado
            |--------------------------------------------------------------------------
            */

            'lawyers' => [

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
            | Actividades por tipo
            |--------------------------------------------------------------------------
            */

            'activities' => [

                'labels' =>

                    array_keys(
                        $activitiesByType
                    ),

                'values' =>

                    array_values(
                        $activitiesByType
                    ),

            ],

            /*
            |--------------------------------------------------------------------------
            | Eventos por tipo
            |--------------------------------------------------------------------------
            */

            'events' => [

                'labels' =>

                    array_keys(
                        $eventsByType
                    ),

                'values' =>

                    array_values(
                        $eventsByType
                    ),

            ],

        ];

        /*
        |--------------------------------------------------------------------------
        | Tabla principal
        |--------------------------------------------------------------------------
        */

        $rows = collect($dataset)

            ->sortByDesc(function ($row) {

                return $row['activities_count'];

            })

            ->values()

            ->map(function ($row) {

                /*
                |--------------------------------------------------------------------------
                | Estado
                |--------------------------------------------------------------------------
                */

                $statusLabel = match ($row['status']) {

                    'open' => 'Abierto',

                    'in_progress' => 'En Proceso',

                    'paused' => 'Pausado',

                    'closed' => 'Cerrado',

                    default => $row['status']

                };

                /*
                |--------------------------------------------------------------------------
                | Última actividad
                |--------------------------------------------------------------------------
                */

                $lastActivity =

                    $row['last_activity']

                        ? Carbon::parse(
                            $row['last_activity']
                        )->format('d/m/Y H:i')

                        : 'Sin actividades';

                /*
                |--------------------------------------------------------------------------
                | Próximo evento
                |--------------------------------------------------------------------------
                */

                $nextEvent =

                    $row['next_event']

                        ? Carbon::parse(
                            $row['next_event']
                        )->format('d/m/Y H:i')

                        : 'Sin programar';

                return [

                    'case_title' =>
                        $row['case_title'],

                    'client' =>
                        $row['client'],

                    'specialty' =>
                        $row['specialty'],

                    'lawyer' =>
                        $row['lawyer'],

                    'status' =>
                        $statusLabel,

                    'activities_count' =>
                        $row['activities_count'],

                    'last_activity' =>
                        $lastActivity,

                    'next_event' =>
                        $nextEvent,

                    /*
                    |--------------------------------------------------------------------------
                    | Valores crudos
                    |--------------------------------------------------------------------------
                    */

                    'activities_count_raw' =>
                        $row['activities_count'],

                ];

            });

        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        return response()->json([

            'summary' => [

                'total_cases' =>

                    $totalCases,

                'active_cases' =>

                    $activeCases,

                'closed_cases' =>

                    $closedCases,

                'paused_cases' =>

                    $pausedCases,

                'activities' =>

                    $totalActivities,

                'events' =>

                    $totalEvents,

                'avg_activities' =>

                    $avgActivitiesPerCase,

                'without_activities' =>

                    $casesWithoutActivities,

                'without_future_events' =>

                    $casesWithoutFutureEvents,

                'without_recent_communication' =>

                    $casesWithoutRecentCommunication,

            ],

            'charts' =>

                $charts,

            'data' =>

                $rows,

        ]);

    }

}