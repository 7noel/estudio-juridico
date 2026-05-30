<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\AgendaEvent;
use App\Models\CaseFile;
use App\Models\Establishment;
use App\Models\LegalSpecialty;
use App\Models\User;

use Carbon\Carbon;

class AgendaReportController extends Controller
{
    public function index()
    {
        $establishments = Establishment::orderBy('name')->get();

        $specialties = LegalSpecialty::orderBy('name')->get();

        $lawyers = User::role('Abogado')
            ->orderBy('name')
            ->get();

        return view(
            'reports.agenda.index',
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
        | Eventos
        |--------------------------------------------------------------------------
        */

        $events = AgendaEvent::query()

            ->with([

                'case',

                'case.client',

                'case.lawyer',

                'case.specialty',

                'creator',

            ]);

        /*
        |--------------------------------------------------------------------------
        | Fechas
        |--------------------------------------------------------------------------
        */

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

        /*
        |--------------------------------------------------------------------------
        | Tipo
        |--------------------------------------------------------------------------
        */

        if ($request->type) {

            $events->where(
                'type',
                $request->type
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Filtro por sede
        |--------------------------------------------------------------------------
        */

        if ($request->establishment_id) {

            $events->whereHas(

                'case',

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
        | Filtro por especialidad
        |--------------------------------------------------------------------------
        */

        if ($request->specialty_id) {

            $events->whereHas(

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
        | Filtro por abogado
        |--------------------------------------------------------------------------
        */

        if ($request->lawyer_id) {

            $events->whereHas(

                'case',

                function ($q) use ($request) {

                    $q->where(
                        'lawyer_id',
                        $request->lawyer_id
                    );

                }

            );

        }

        $events = $events->get();

        /*
        |--------------------------------------------------------------------------
        | Dataset
        |--------------------------------------------------------------------------
        */

        $dataset = [];

        $eventsToday = 0;

        $upcoming7Days = 0;

        $upcomingDeadlines = 0;

        $eventsWithoutCase = 0;

        foreach ($events as $event) {

            /*
            |--------------------------------------------------------------------------
            | Hoy
            |--------------------------------------------------------------------------
            */

            if (

                Carbon::parse(
                    $event->start_datetime
                )->isToday()

            ) {

                $eventsToday++;

            }

            /*
            |--------------------------------------------------------------------------
            | Próximos 7 días
            |--------------------------------------------------------------------------
            */

            if (

                Carbon::parse(
                    $event->start_datetime
                )

                ->between(

                    now(),

                    now()->copy()->addDays(7)

                )

            ) {

                $upcoming7Days++;

            }

            /*
            |--------------------------------------------------------------------------
            | Vencimientos próximos
            |--------------------------------------------------------------------------
            */

            if (

                $event->type === 'deadline'

                &&

                Carbon::parse(
                    $event->start_datetime
                )

                ->between(

                    now(),

                    now()->copy()->addDays(7)

                )

            ) {

                $upcomingDeadlines++;

            }

            /*
            |--------------------------------------------------------------------------
            | Sin caso
            |--------------------------------------------------------------------------
            */

            if (

                !$event->case_id

            ) {

                $eventsWithoutCase++;

            }

            $dataset[] = [

                'event_id' =>

                    $event->id,

                'date' =>

                    $event->start_datetime,

                'title' =>

                    $event->title,

                'type' =>

                    $event->type,

                'location' =>

                    $event->location,

                'case_title' =>

                    optional(
                        $event->case
                    )->title,

                'client_name' =>

                    optional(
                        optional(
                            $event->case
                        )->client
                    )->full_name,

                'lawyer_name' =>

                    optional(
                        optional(
                            $event->case
                        )->lawyer
                    )->name,

                'specialty' =>

                    optional(
                        optional(
                            $event->case
                        )->specialty
                    )->name,

                'creator' =>

                    optional(
                        $event->creator
                    )->name,

            ];

        }

        /*
        |--------------------------------------------------------------------------
        | Total eventos
        |--------------------------------------------------------------------------
        */

        $totalEvents =
            count($dataset);

        /*
        |--------------------------------------------------------------------------
        | Próximo evento
        |--------------------------------------------------------------------------
        */

        $nextEvent =

            collect($dataset)

                ->filter(function ($item) {

                    return

                        Carbon::parse(
                            $item['date']
                        )

                        ->gte(now());

                })

                ->sortBy('date')

                ->first();

        /*
        |--------------------------------------------------------------------------
        | Casos sin próximo evento
        |--------------------------------------------------------------------------
        */

        $casesWithoutNextEvent =

            CaseFile::query()

                ->when(

                    $request->establishment_id,

                    function ($q) use ($request) {

                        $q->where(
                            'establishment_id',
                            $request->establishment_id
                        );

                    }

                )

                ->when(

                    $request->specialty_id,

                    function ($q) use ($request) {

                        $q->where(
                            'legal_specialty_id',
                            $request->specialty_id
                        );

                    }

                )

                ->when(

                    $request->lawyer_id,

                    function ($q) use ($request) {

                        $q->where(
                            'lawyer_id',
                            $request->lawyer_id
                        );

                    }

                )

                ->whereDoesntHave(

                    'agendaEvents',

                    function ($q) {

                        $q->where(
                            'start_datetime',
                            '>=',
                            now()
                        );

                    }

                )

                ->count();

        /*
        |--------------------------------------------------------------------------
        | Eventos por abogado
        |--------------------------------------------------------------------------
        */

        $eventsByLawyer = [];

        foreach ($dataset as $row) {

            $lawyer =

                $row['lawyer_name']
                ?: 'Sin abogado';

            if (!isset(
                $eventsByLawyer[$lawyer]
            )) {

                $eventsByLawyer[$lawyer] = 0;

            }

            $eventsByLawyer[$lawyer]++;

        }

        /*
        |--------------------------------------------------------------------------
        | Abogado con mayor carga
        |--------------------------------------------------------------------------
        */

        arsort($eventsByLawyer);

        $topLawyerName =
            array_key_first(
                $eventsByLawyer
            );

        $topLawyerEvents =
            reset(
                $eventsByLawyer
            );

        /*
        |--------------------------------------------------------------------------
        | Eventos por tipo
        |--------------------------------------------------------------------------
        */

        $eventsByType = [];

        foreach ($dataset as $row) {

            $type =
                $row['type']
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
        | Eventos por mes
        |--------------------------------------------------------------------------
        */

        $eventsByMonth = [];

        foreach ($dataset as $row) {

            $month =

                Carbon::parse(
                    $row['date']
                )

                ->format('Y-m');

            if (!isset(
                $eventsByMonth[$month]
            )) {

                $eventsByMonth[$month] = 0;

            }

            $eventsByMonth[$month]++;

        }

        /*
        |--------------------------------------------------------------------------
        | Eventos por especialidad
        |--------------------------------------------------------------------------
        */

        $eventsBySpecialty = [];

        foreach ($dataset as $row) {

            $specialty =

                $row['specialty']
                ?: 'Sin especialidad';

            if (!isset(
                $eventsBySpecialty[$specialty]
            )) {

                $eventsBySpecialty[$specialty] = 0;

            }

            $eventsBySpecialty[$specialty]++;

        }

        /*
        |--------------------------------------------------------------------------
        | Ordenamientos
        |--------------------------------------------------------------------------
        */

        arsort($eventsByType);

        arsort($eventsByMonth);

        arsort($eventsBySpecialty);

        /*
        |--------------------------------------------------------------------------
        | Charts
        |--------------------------------------------------------------------------
        */

        $charts = [

            /*
            |--------------------------------------------------------------------------
            | Eventos por tipo
            |--------------------------------------------------------------------------
            */

            'types' => [

                'labels' =>

                    array_keys(
                        $eventsByType
                    ),

                'values' =>

                    array_values(
                        $eventsByType
                    ),

            ],

            /*
            |--------------------------------------------------------------------------
            | Eventos por mes
            |--------------------------------------------------------------------------
            */

            'months' => [

                'labels' =>

                    array_keys(
                        $eventsByMonth
                    ),

                'values' =>

                    array_values(
                        $eventsByMonth
                    ),

            ],

            /*
            |--------------------------------------------------------------------------
            | Eventos por abogado
            |--------------------------------------------------------------------------
            */

            'lawyers' => [

                'labels' =>

                    array_keys(
                        $eventsByLawyer
                    ),

                'values' =>

                    array_values(
                        $eventsByLawyer
                    ),

            ],

            /*
            |--------------------------------------------------------------------------
            | Eventos por especialidad
            |--------------------------------------------------------------------------
            */

            'specialties' => [

                'labels' =>

                    array_keys(
                        $eventsBySpecialty
                    ),

                'values' =>

                    array_values(
                        $eventsBySpecialty
                    ),

            ],

        ];

        /*
        |--------------------------------------------------------------------------
        | Tabla principal
        |--------------------------------------------------------------------------
        */

        $rows = collect($dataset)

            ->sortBy('date')

            ->values()

            ->map(function ($row) {

                $date = Carbon::parse(
                    $row['date']
                );

                return [

                    'date' =>

                        $date->format(
                            'd/m/Y H:i'
                        ),

                    'title' =>

                        $row['title'],

                    'type' =>

                        $row['type'],

                    'case_title' =>

                        $row['case_title']
                        ?: '-',

                    'client_name' =>

                        $row['client_name']
                        ?: '-',

                    'lawyer_name' =>

                        $row['lawyer_name']
                        ?: '-',

                    'location' =>

                        $row['location']
                        ?: '-',

                    'creator' =>

                        $row['creator']
                        ?: '-',

                    /*
                    |--------------------------------------------------------------------------
                    | Valores crudos
                    |--------------------------------------------------------------------------
                    */

                    'date_raw' =>

                        $row['date'],

                ];

            });

        /*
        |--------------------------------------------------------------------------
        | Próximo evento
        |--------------------------------------------------------------------------
        */

        $nextEventTitle = '-';

        $nextEventDate = '-';

        if ($nextEvent) {

            $nextEventTitle =

                $nextEvent['title'];

            $nextEventDate =

                Carbon::parse(
                    $nextEvent['date']
                )

                ->format(
                    'd/m/Y H:i'
                );

        }

        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        return response()->json([

            'summary' => [

                'total_events' =>

                    $totalEvents,

                'events_today' =>

                    $eventsToday,

                'upcoming_7_days' =>

                    $upcoming7Days,

                'upcoming_deadlines' =>

                    $upcomingDeadlines,

                'cases_without_next_event' =>

                    $casesWithoutNextEvent,

                'events_without_case' =>

                    $eventsWithoutCase,

                'top_lawyer_name' =>

                    $topLawyerName
                    ?: '-',

                'top_lawyer_events' =>

                    $topLawyerEvents
                    ?: 0,

                'next_event_title' =>

                    $nextEventTitle,

                'next_event_date' =>

                    $nextEventDate,

            ],

            'charts' =>

                $charts,

            'data' =>

                $rows,

        ]);

    }

}