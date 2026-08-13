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
        | Configuración
        |--------------------------------------------------------------------------
        */

        $inactiveDays = (int) NotificationSetting::get('client_inactivity_days', 15);

        /*
        |--------------------------------------------------------------------------
        | Query base con filtros
        |--------------------------------------------------------------------------
        */

        $cases = CaseFile::query()
            ->with([
                'client',
                'lawyer',
                'specialty',
                'establishment',
            ])
            ->withCount('activities')
            ->withMax('activities', 'activity_at')
            ->withMax('activities as last_communication_at', 'activity_at')
            ->withCount('agendaEvents')
            ->withMin('agendaEvents as next_event_at', 'start_datetime');

        // Fecha
        if ($request->date_from) {
            $cases->whereDate('opened_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $cases->whereDate('opened_at', '<=', $request->date_to);
        }

        // Sede
        if ($request->establishment_id) {
            $cases->where('establishment_id', $request->establishment_id);
        }

        // Especialidad
        if ($request->specialty_id) {
            $cases->where('legal_specialty_id', $request->specialty_id);
        }

        // Abogado
        if ($request->lawyer_id) {
            $cases->where('lawyer_id', $request->lawyer_id);
        }

        // Estado
        if ($request->status) {
            $cases->where('status', $request->status);
        }

        $cases = $cases->get();

        /*
        |--------------------------------------------------------------------------
        | Dataset principal - usando withCount/withMax/withMin
        |--------------------------------------------------------------------------
        */

        $dataset = [];
        $totalActivities = 0;
        $totalEvents = 0;
        $casesWithoutActivities = 0;
        $casesWithoutFutureEvents = 0;
        $casesWithoutRecentCommunication = 0;

        foreach ($cases as $case) {
            $activitiesCount = $case->activities_count;
            $totalActivities += $activitiesCount;

            if ($activitiesCount == 0) {
                $casesWithoutActivities++;
            }

            $lastActivity = $case->activities_max_activity_at;
            $lastCommunication = $case->last_communication_at;

            // Comunicación reciente
            if ($case->status === 'in_progress') {
                $referenceDate = $lastCommunication ?? $case->opened_at;
                if ($referenceDate) {
                    $days = Carbon::parse($referenceDate)->diffInDays(now());
                    if ($days >= $inactiveDays) {
                        $casesWithoutRecentCommunication++;
                    }
                }
            }

            $nextEvent = $case->next_event_at;
            if (!$nextEvent || Carbon::parse($nextEvent)->lt(now())) {
                $casesWithoutFutureEvents++;
            }

            $totalEvents += $case->agenda_events_count;

            $dataset[] = [
                'case_id' => $case->id,
                'case_title' => $case->title,
                'client' => optional($case->client)->full_name,
                'specialty' => optional($case->specialty)->name,
                'lawyer' => optional($case->lawyer)->name,
                'status' => $case->status,
                'activities_count' => $activitiesCount,
                'last_activity' => $lastActivity,
                'next_event' => $nextEvent,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | KPIs
        |--------------------------------------------------------------------------
        */

        $totalCases = count($dataset);

        $activeCases = collect($dataset)->whereIn('status', ['open', 'in_progress'])->count();
        $closedCases = collect($dataset)->where('status', 'closed')->count();
        $pausedCases = collect($dataset)->where('status', 'paused')->count();

        $avgActivitiesPerCase = $totalCases > 0
            ? round($totalActivities / $totalCases, 2)
            : 0;

        /*
        |--------------------------------------------------------------------------
        | Casos por Estado (GROUP BY en SQL)
        |--------------------------------------------------------------------------
        */

        $casesByStatusQuery = CaseFile::query()
            ->when($request->date_from, function ($q) use ($request) {
                $q->whereDate('opened_at', '>=', $request->date_from);
            })
            ->when($request->date_to, function ($q) use ($request) {
                $q->whereDate('opened_at', '<=', $request->date_to);
            })
            ->when($request->establishment_id, function ($q) use ($request) {
                $q->where('establishment_id', $request->establishment_id);
            })
            ->when($request->specialty_id, function ($q) use ($request) {
                $q->where('legal_specialty_id', $request->specialty_id);
            })
            ->when($request->lawyer_id, function ($q) use ($request) {
                $q->where('lawyer_id', $request->lawyer_id);
            })
            ->when($request->status, function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        $casesByStatus = [];
        foreach ($casesByStatusQuery as $row) {
            $casesByStatus[$row->status] = $row->count;
        }

        /*
        |--------------------------------------------------------------------------
        | Casos por Especialidad (GROUP BY en SQL)
        |--------------------------------------------------------------------------
        */

        $casesBySpecialtyQuery = CaseFile::query()
            ->when($request->date_from, function ($q) use ($request) {
                $q->whereDate('opened_at', '>=', $request->date_from);
            })
            ->when($request->date_to, function ($q) use ($request) {
                $q->whereDate('opened_at', '<=', $request->date_to);
            })
            ->when($request->establishment_id, function ($q) use ($request) {
                $q->where('establishment_id', $request->establishment_id);
            })
            ->when($request->specialty_id, function ($q) use ($request) {
                $q->where('legal_specialty_id', $request->specialty_id);
            })
            ->when($request->lawyer_id, function ($q) use ($request) {
                $q->where('lawyer_id', $request->lawyer_id);
            })
            ->when($request->status, function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->selectRaw('COALESCE(legal_specialties.name, "Sin especialidad") as name, COUNT(*) as count')
            ->leftJoin('legal_specialties', 'cases.legal_specialty_id', '=', 'legal_specialties.id')
            ->groupBy('cases.legal_specialty_id')
            ->get();

        $casesBySpecialty = [];
        foreach ($casesBySpecialtyQuery as $row) {
            $casesBySpecialty[$row->name] = $row->count;
        }

        /*
        |--------------------------------------------------------------------------
        | Casos por Abogado (GROUP BY en SQL)
        |--------------------------------------------------------------------------
        */

        $casesByLawyerQuery = CaseFile::query()
            ->when($request->date_from, function ($q) use ($request) {
                $q->whereDate('opened_at', '>=', $request->date_from);
            })
            ->when($request->date_to, function ($q) use ($request) {
                $q->whereDate('opened_at', '<=', $request->date_to);
            })
            ->when($request->establishment_id, function ($q) use ($request) {
                $q->where('establishment_id', $request->establishment_id);
            })
            ->when($request->specialty_id, function ($q) use ($request) {
                $q->where('legal_specialty_id', $request->specialty_id);
            })
            ->when($request->lawyer_id, function ($q) use ($request) {
                $q->where('lawyer_id', $request->lawyer_id);
            })
            ->when($request->status, function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->selectRaw('COALESCE(users.name, "Sin abogado") as name, COUNT(*) as count')
            ->leftJoin('users', 'cases.lawyer_id', '=', 'users.id')
            ->groupBy('cases.lawyer_id')
            ->get();

        $casesByLawyer = [];
        foreach ($casesByLawyerQuery as $row) {
            $casesByLawyer[$row->name] = $row->count;
        }

        /*
        |--------------------------------------------------------------------------
        | Actividades por Tipo (GROUP BY en SQL)
        |--------------------------------------------------------------------------
        */

        $activitiesByTypeQuery = CaseActivity::query()
            ->whereIn('case_id', $cases->pluck('id'))
            ->when($request->date_from, function ($q) use ($request) {
                $q->whereDate('activity_at', '>=', $request->date_from);
            })
            ->when($request->date_to, function ($q) use ($request) {
                $q->whereDate('activity_at', '<=', $request->date_to);
            })
            ->selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->get();

        $activitiesByType = [];
        foreach ($activitiesByTypeQuery as $row) {
            $activitiesByType[$row->type ?: 'Sin tipo'] = $row->count;
        }

        /*
        |--------------------------------------------------------------------------
        | Eventos por Tipo (GROUP BY en SQL)
        |--------------------------------------------------------------------------
        */

        $eventsByTypeQuery = AgendaEvent::query()
            ->whereIn('case_id', $cases->pluck('id'))
            ->when($request->date_from, function ($q) use ($request) {
                $q->whereDate('start_datetime', '>=', $request->date_from);
            })
            ->when($request->date_to, function ($q) use ($request) {
                $q->whereDate('start_datetime', '<=', $request->date_to);
            })
            ->selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->get();

        $eventsByType = [];
        foreach ($eventsByTypeQuery as $row) {
            $eventsByType[$row->type ?: 'Sin tipo'] = $row->count;
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
            'status' => [
                'labels' => array_keys($casesByStatus),
                'values' => array_values($casesByStatus),
            ],
            'specialties' => [
                'labels' => array_keys($casesBySpecialty),
                'values' => array_values($casesBySpecialty),
            ],
            'lawyers' => [
                'labels' => array_keys($casesByLawyer),
                'values' => array_values($casesByLawyer),
            ],
            'activities' => [
                'labels' => array_keys($activitiesByType),
                'values' => array_values($activitiesByType),
            ],
            'events' => [
                'labels' => array_keys($eventsByType),
                'values' => array_values($eventsByType),
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | Tabla principal
        |--------------------------------------------------------------------------
        */

        $rows = collect($dataset)
            ->sortByDesc('activities_count')
            ->values()
            ->map(function ($row) {
                $statusLabel = match ($row['status']) {
                    'open' => 'Abierto',
                    'in_progress' => 'En Proceso',
                    'paused' => 'Pausado',
                    'closed' => 'Cerrado',
                    default => $row['status']
                };

                $lastActivity = $row['last_activity']
                    ? Carbon::parse($row['last_activity'])->format('d/m/Y H:i')
                    : 'Sin actividades';

                $nextEvent = $row['next_event']
                    ? Carbon::parse($row['next_event'])->format('d/m/Y H:i')
                    : 'Sin programar';

                return [
                    'case_title' => $row['case_title'],
                    'client' => $row['client'],
                    'specialty' => $row['specialty'],
                    'lawyer' => $row['lawyer'],
                    'status' => $statusLabel,
                    'activities_count' => $row['activities_count'],
                    'last_activity' => $lastActivity,
                    'next_event' => $nextEvent,
                    'activities_count_raw' => $row['activities_count'],
                ];
            });

        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'summary' => [
                'total_cases' => $totalCases,
                'active_cases' => $activeCases,
                'closed_cases' => $closedCases,
                'paused_cases' => $pausedCases,
                'activities' => $totalActivities,
                'events' => $totalEvents,
                'avg_activities' => $avgActivitiesPerCase,
                'without_activities' => $casesWithoutActivities,
                'without_future_events' => $casesWithoutFutureEvents,
                'without_recent_communication' => $casesWithoutRecentCommunication,
            ],
            'charts' => $charts,
            'data' => $rows,
        ]);
    }
}
