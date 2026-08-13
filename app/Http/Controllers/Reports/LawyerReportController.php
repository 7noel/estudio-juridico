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
        | Configuración
        |--------------------------------------------------------------------------
        */

        $inactiveDays = (int) NotificationSetting::get('client_inactivity_days', 15);

        /*
        |--------------------------------------------------------------------------
        | Query base con filtros y withCount/withSum/withMax
        |--------------------------------------------------------------------------
        */

        $cases = CaseFile::query()
            ->with([
                'lawyer',
                'client',
                'specialty',
                'establishment',
                'consultation.payments',
                'expenses',
            ])
            ->withCount('activities')
            ->withCount('agendaEvents')
            ->withMax('activities as last_communication_at', 'activity_at');

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
        | Dataset por abogado - usando withCount/withSum/withMax
        |--------------------------------------------------------------------------
        */

        $lawyersData = [];

        foreach ($cases as $case) {
            $lawyerId = $case->lawyer_id;
            $lawyerName = optional($case->lawyer)->name ?? 'Sin abogado';

            if (!isset($lawyersData[$lawyerId])) {
                $lawyersData[$lawyerId] = [
                    'lawyer_id' => $lawyerId,
                    'lawyer_name' => $lawyerName,
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

            $lawyersData[$lawyerId]['cases']++;

            if (in_array($case->status, ['open', 'in_progress'])) {
                $lawyersData[$lawyerId]['active_cases']++;
            }

            if ($case->status === 'closed') {
                $lawyersData[$lawyerId]['closed_cases']++;
            }

            $lawyersData[$lawyerId]['activities'] += $case->activities_count;
            $lawyersData[$lawyerId]['events'] += $case->agenda_events_count;

            $income = $case->consultation ? $case->consultation->payments->sum('amount') : 0;
            $lawyersData[$lawyerId]['income'] += $income;

            $expense = $case->expenses->sum('amount');
            $lawyersData[$lawyerId]['expense'] += $expense;

            // Comunicación reciente
            if ($case->status === 'in_progress') {
                $lastCommunication = $case->last_communication_at;
                $referenceDate = $lastCommunication ?? $case->opened_at;
                if ($referenceDate) {
                    $days = Carbon::parse($referenceDate)->diffInDays(now());
                    if ($days >= $inactiveDays) {
                        $lawyersData[$lawyerId]['inactive_cases']++;
                    }
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Calcular utilidad y margen
        |--------------------------------------------------------------------------
        */

        foreach ($lawyersData as &$lawyer) {
            $lawyer['profit'] = $lawyer['income'] - $lawyer['expense'];
            $lawyer['margin'] = $lawyer['income'] > 0
                ? round(($lawyer['profit'] * 100) / $lawyer['income'], 2)
                : 0;
            $lawyer['avg_activities'] = $lawyer['cases'] > 0
                ? round($lawyer['activities'] / $lawyer['cases'], 2)
                : 0;
        }
        unset($lawyer);

        /*
        |--------------------------------------------------------------------------
        | KPIs generales
        |--------------------------------------------------------------------------
        */

        $dataset = collect($lawyersData);

        $totalLawyers = $dataset->count();
        $totalCases = $dataset->sum('cases');
        $totalActiveCases = $dataset->sum('active_cases');
        $totalClosedCases = $dataset->sum('closed_cases');
        $totalActivities = $dataset->sum('activities');
        $totalEvents = $dataset->sum('events');
        $totalIncome = $dataset->sum('income');
        $totalProfit = $dataset->sum('profit');

        /*
        |--------------------------------------------------------------------------
        | Abogado más rentable
        |--------------------------------------------------------------------------
        */

        $bestLawyer = $dataset->sortByDesc('profit')->first();

        /*
        |--------------------------------------------------------------------------
        | Casos sin comunicación reciente
        |--------------------------------------------------------------------------
        */

        $inactiveCases = $dataset->sum('inactive_cases');

        /*
        |--------------------------------------------------------------------------
        | Promedio general actividades/caso
        |--------------------------------------------------------------------------
        */

        $avgActivitiesPerCase = $totalCases > 0
            ? round($totalActivities / $totalCases, 2)
            : 0;

        /*
        |--------------------------------------------------------------------------
        | Ranking ingresos
        |--------------------------------------------------------------------------
        */

        $rankingIncome = $dataset->sortByDesc('income')->values();

        /*
        |--------------------------------------------------------------------------
        | Ranking utilidad
        |--------------------------------------------------------------------------
        */

        $rankingProfit = $dataset->sortByDesc('profit')->values();

        /*
        |--------------------------------------------------------------------------
        | Casos por abogado (GROUP BY en SQL)
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
        | Actividades por abogado (GROUP BY en SQL)
        |--------------------------------------------------------------------------
        */

        $activitiesByLawyerQuery = CaseActivity::query()
            ->whereIn('case_id', $cases->pluck('id'))
            ->selectRaw('COALESCE(users.name, "Sin abogado") as name, COUNT(*) as count')
            ->join('cases', 'case_activities.case_id', '=', 'cases.id')
            ->leftJoin('users', 'cases.lawyer_id', '=', 'users.id')
            ->groupBy('cases.lawyer_id')
            ->get();

        $activitiesByLawyer = [];
        foreach ($activitiesByLawyerQuery as $row) {
            $activitiesByLawyer[$row->name] = $row->count;
        }

        /*
        |--------------------------------------------------------------------------
        | Casos por estado (GROUP BY en SQL)
        |--------------------------------------------------------------------------
        */

        $statusTotals = [
            'Activos' => 0,
            'Cerrados' => 0,
            'Otros' => 0,
        ];

        $statusCountsQuery = CaseFile::query()
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
            ->selectRaw("
                SUM(CASE WHEN status IN ('open', 'in_progress') THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN status = 'closed' THEN 1 ELSE 0 END) as closed,
                SUM(CASE WHEN status NOT IN ('open', 'in_progress', 'closed') THEN 1 ELSE 0 END) as other
            ")
            ->first();

        $statusTotals['Activos'] = $statusCountsQuery->active ?? 0;
        $statusTotals['Cerrados'] = $statusCountsQuery->closed ?? 0;
        $statusTotals['Otros'] = $statusCountsQuery->other ?? 0;

        /*
        |--------------------------------------------------------------------------
        | Charts
        |--------------------------------------------------------------------------
        */

        $charts = [
            'income' => [
                'labels' => $rankingIncome->pluck('lawyer_name')->values(),
                'values' => $rankingIncome->pluck('income')->values(),
            ],
            'profit' => [
                'labels' => $rankingProfit->pluck('lawyer_name')->values(),
                'values' => $rankingProfit->pluck('profit')->values(),
            ],
            'cases' => [
                'labels' => array_keys($casesByLawyer),
                'values' => array_values($casesByLawyer),
            ],
            'activities' => [
                'labels' => array_keys($activitiesByLawyer),
                'values' => array_values($activitiesByLawyer),
            ],
            'status' => [
                'labels' => array_keys($statusTotals),
                'values' => array_values($statusTotals),
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | Tabla principal
        |--------------------------------------------------------------------------
        */

        $rows = $dataset
            ->sortByDesc('profit')
            ->values()
            ->map(function ($lawyer) {
                return [
                    'lawyer_name' => $lawyer['lawyer_name'],
                    'cases' => $lawyer['cases'],
                    'active_cases' => $lawyer['active_cases'],
                    'closed_cases' => $lawyer['closed_cases'],
                    'activities' => $lawyer['activities'],
                    'events' => $lawyer['events'],
                    'inactive_cases' => $lawyer['inactive_cases'],
                    'avg_activities' => number_format($lawyer['avg_activities'], 2),
                    'income' => number_format($lawyer['income'], 2),
                    'expense' => number_format($lawyer['expense'], 2),
                    'profit' => number_format($lawyer['profit'], 2),
                    'margin' => number_format($lawyer['margin'], 2) . '%',
                    'income_raw' => $lawyer['income'],
                    'expense_raw' => $lawyer['expense'],
                    'profit_raw' => $lawyer['profit'],
                    'margin_raw' => $lawyer['margin'],
                ];
            });

        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'summary' => [
                'total_lawyers' => $totalLawyers,
                'total_cases' => $totalCases,
                'active_cases' => $totalActiveCases,
                'closed_cases' => $totalClosedCases,
                'activities' => $totalActivities,
                'events' => $totalEvents,
                'income' => round($totalIncome, 2),
                'profit' => round($totalProfit, 2),
                'inactive_cases' => $inactiveCases,
                'avg_activities' => $avgActivitiesPerCase,
                'best_lawyer_name' => $bestLawyer['lawyer_name'] ?? '-',
                'best_lawyer_profit' => round($bestLawyer['profit'] ?? 0, 2),
            ],
            'charts' => $charts,
            'data' => $rows,
        ]);
    }
}
