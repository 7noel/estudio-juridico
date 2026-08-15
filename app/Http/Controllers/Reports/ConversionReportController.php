<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Consultation;
use App\Models\ConsultationFollowUp;
use App\Models\CaseFile;
use App\Models\Establishment;
use App\Models\LegalSpecialty;
use App\Models\User;

use Carbon\Carbon;

class ConversionReportController extends Controller
{
    public function index()
    {
        $establishments = Establishment::orderBy('name')->get();
        $specialties = LegalSpecialty::orderBy('name')->get();
        $lawyers = User::role('Abogado')->orderBy('name')->get();

        return view(
            'reports.conversion.index',
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
        | Query base con filtros y withCount/withMax
        |--------------------------------------------------------------------------
        */

        $consultations = Consultation::query()
            ->with([
                'client',
                'lawyer',
                'specialty',
                'subject',
                'case',
                'lastFollowUp',
            ])
            ->withCount('followUps')
            ->withMax('followUps', 'contact_date');

        // Fecha de creación
        if ($request->date_from) {
            $consultations->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $consultations->whereDate('created_at', '<=', $request->date_to);
        }

        // Sede
        if ($request->establishment_id) {
            $consultations->where('establishment_id', $request->establishment_id);
        }

        // Especialidad
        if ($request->specialty_id) {
            $consultations->where('legal_specialty_id', $request->specialty_id);
        }

        // Abogado
        if ($request->lawyer_id) {
            $consultations->where('lawyer_id', $request->lawyer_id);
        }

        // Estado
        if ($request->status) {
            $consultations->where('status', $request->status);
        }

        $consultations = $consultations->get();

        /*
        |--------------------------------------------------------------------------
        | Dataset - usando withCount/withMax
        |--------------------------------------------------------------------------
        */

        $dataset = [];
        $totalFollowUps = 0;
        $totalConverted = 0;
        $totalAccepted = 0;
        $totalRejected = 0;
        $totalInProcess = 0;
        $conversionDays = [];

        foreach ($consultations as $consultation) {
            $followUpsCount = $consultation->follow_ups_count;
            $totalFollowUps += $followUpsCount;

            $hasCase = $consultation->case ? true : false;

            if ($hasCase) {
                $totalConverted++;

                if ($consultation->accepted_at) {
                    $conversionDays[] = Carbon::parse($consultation->created_at)->diffInDays($consultation->accepted_at);
                }
            }

            if ($consultation->status === 'accepted') {
                $totalAccepted++;
            }

            if ($consultation->status === 'rejected') {
                $totalRejected++;
            }

            if (in_array($consultation->status, ['new', 'prospect'])) {
                $totalInProcess++;
            }

            $dataset[] = [
                'consultation_id' => $consultation->id,
                'title' => $consultation->title,
                'client' => optional($consultation->client)->full_name,
                'lawyer' => optional($consultation->lawyer)->name,
                'specialty' => optional($consultation->specialty)->name,
                'status' => $consultation->status,
                'created_at' => $consultation->created_at,
                'follow_ups_count' => $followUpsCount,
                'last_result' => optional($consultation->lastFollowUp)->result,
                'last_contact' => $consultation->follow_ups_max_contact_date,
                'next_contact' => $consultation->next_follow_up_at,
                'converted' => $hasCase,
                'converted_at' => $consultation->accepted_at,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | KPIs
        |--------------------------------------------------------------------------
        */

        $totalConsultations = count($dataset);

        $conversionRate = $totalConsultations > 0
            ? round(($totalConverted * 100) / $totalConsultations, 2)
            : 0;

        $avgFollowUps = $totalConsultations > 0
            ? round($totalFollowUps / $totalConsultations, 2)
            : 0;

        $avgConversionDays = count($conversionDays) > 0
            ? round(array_sum($conversionDays) / count($conversionDays), 1)
            : 0;

        /*
        |--------------------------------------------------------------------------
        | Embudo por estado (GROUP BY en SQL)
        |--------------------------------------------------------------------------
        */

        $funnelQuery = Consultation::query()
            ->when($request->date_from, function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->date_from);
            })
            ->when($request->date_to, function ($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->date_to);
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

        $funnel = [];
        foreach ($funnelQuery as $row) {
            $funnel[$row->status] = $row->count;
        }

        /*
        |--------------------------------------------------------------------------
        | Conversión por especialidad (GROUP BY en SQL)
        |--------------------------------------------------------------------------
        */

        $conversionBySpecialtyQuery = Consultation::query()
            ->when($request->date_from, function ($q) use ($request) {
                $q->whereDate('consultations.created_at', '>=', $request->date_from);
            })
            ->when($request->date_to, function ($q) use ($request) {
                $q->whereDate('consultations.created_at', '<=', $request->date_to);
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
            ->selectRaw('
                COALESCE(legal_specialties.name, "Sin especialidad") as name,
                COUNT(*) as total,
                SUM(CASE WHEN cases.id IS NOT NULL THEN 1 ELSE 0 END) as converted
            ')
            ->leftJoin('legal_specialties', 'consultations.legal_specialty_id', '=', 'legal_specialties.id')
            ->leftJoin('cases', 'consultations.id', '=', 'cases.consultation_id')
            ->groupBy('consultations.legal_specialty_id')
            ->get();

        $conversionBySpecialty = [];
        foreach ($conversionBySpecialtyQuery as $row) {
            $conversionBySpecialty[$row->name] = [
                'total' => $row->total,
                'converted' => $row->converted,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Conversión por abogado (GROUP BY en SQL)
        |--------------------------------------------------------------------------
        */

        $conversionByLawyerQuery = Consultation::query()
            ->when($request->date_from, function ($q) use ($request) {
                $q->whereDate('consultations.created_at', '>=', $request->date_from);
            })
            ->when($request->date_to, function ($q) use ($request) {
                $q->whereDate('consultations.created_at', '<=', $request->date_to);
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
                COALESCE(users.name, 'Sin abogado') as name,
                COUNT(*) as total,
                SUM(CASE WHEN cases.id IS NOT NULL THEN 1 ELSE 0 END) as converted
            ")
            ->leftJoin('users', 'consultations.lawyer_id', '=', 'users.id')
            ->leftJoin('cases', 'consultations.id', '=', 'cases.consultation_id')
            ->groupBy('consultations.lawyer_id')
            ->get();

        $conversionByLawyer = [];
        foreach ($conversionByLawyerQuery as $row) {
            $conversionByLawyer[$row->name] = [
                'total' => $row->total,
                'converted' => $row->converted,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Motivos de rechazo / resultados de seguimiento (GROUP BY en SQL)
        |--------------------------------------------------------------------------
        */

        $followUpResultsQuery = ConsultationFollowUp::query()
            ->whereIn('consultation_id', $consultations->pluck('id'))
            ->when($request->date_from, function ($q) use ($request) {
                $q->whereDate('contact_date', '>=', $request->date_from);
            })
            ->when($request->date_to, function ($q) use ($request) {
                $q->whereDate('contact_date', '<=', $request->date_to);
            })
            ->selectRaw('result, COUNT(*) as count')
            ->groupBy('result')
            ->get();

        $followUpResults = [];
        foreach ($followUpResultsQuery as $row) {
            $followUpResults[$row->result ?: 'Sin resultado'] = $row->count;
        }

        /*
        |--------------------------------------------------------------------------
        | Canales de comunicación (GROUP BY en SQL)
        |--------------------------------------------------------------------------
        */

        $communicationChannelsQuery = ConsultationFollowUp::query()
            ->whereIn('consultation_id', $consultations->pluck('id'))
            ->when($request->date_from, function ($q) use ($request) {
                $q->whereDate('contact_date', '>=', $request->date_from);
            })
            ->when($request->date_to, function ($q) use ($request) {
                $q->whereDate('contact_date', '<=', $request->date_to);
            })
            ->selectRaw('communication_type, COUNT(*) as count')
            ->groupBy('communication_type')
            ->get();

        $communicationChannels = [];
        foreach ($communicationChannelsQuery as $row) {
            $communicationChannels[$row->communication_type ?: 'Sin canal'] = $row->count;
        }

        /*
        |--------------------------------------------------------------------------
        | Evolución mensual (GROUP BY en SQL)
        |--------------------------------------------------------------------------
        */

        $monthlyEvolutionQuery = Consultation::query()
            ->when($request->date_from, function ($q) use ($request) {
                $q->whereDate('consultations.created_at', '>=', $request->date_from);
            })
            ->when($request->date_to, function ($q) use ($request) {
                $q->whereDate('consultations.created_at', '<=', $request->date_to);
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
                DATE_FORMAT(consultations.created_at, '%Y-%m') as month,
                COUNT(*) as consultations,
                SUM(CASE WHEN cases.id IS NOT NULL THEN 1 ELSE 0 END) as converted
            ")
            ->leftJoin('cases', 'consultations.id', '=', 'cases.consultation_id')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $monthlyEvolution = [];
        foreach ($monthlyEvolutionQuery as $row) {
            $monthlyEvolution[$row->month] = [
                'consultations' => $row->consultations,
                'converted' => $row->converted,
            ];
        }

        ksort($monthlyEvolution);

        /*
        |--------------------------------------------------------------------------
        | Charts
        |--------------------------------------------------------------------------
        */

        $charts = [
            'funnel' => [
                'labels' => array_keys($funnel),
                'values' => array_values($funnel),
            ],
            'specialties' => [
                'labels' => array_keys($conversionBySpecialty),
                'converted' => array_map(fn($v) => $v['converted'], $conversionBySpecialty),
                'total' => array_map(fn($v) => $v['total'], $conversionBySpecialty),
            ],
            'lawyers' => [
                'labels' => array_keys($conversionByLawyer),
                'converted' => array_map(fn($v) => $v['converted'], $conversionByLawyer),
                'total' => array_map(fn($v) => $v['total'], $conversionByLawyer),
            ],
            'results' => [
                'labels' => array_keys($followUpResults),
                'values' => array_values($followUpResults),
            ],
            'channels' => [
                'labels' => array_keys($communicationChannels),
                'values' => array_values($communicationChannels),
            ],
            'monthly' => [
                'labels' => array_keys($monthlyEvolution),
                'consultations' => array_map(fn($v) => $v['consultations'], $monthlyEvolution),
                'converted' => array_map(fn($v) => $v['converted'], $monthlyEvolution),
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | Tabla principal
        |--------------------------------------------------------------------------
        */

        $rows = collect($dataset)
            ->sortByDesc('created_at')
            ->values()
            ->map(function ($row) {
                $statusLabel = config('options.consultation_statuses')[$row['status']] ?? $row['status'];
                $statusColor = config('options.consultation_status_colors')[$row['status']] ?? 'secondary';
                $lastResultLabel = config('options.follow_up_results')[$row['last_result']] ?? $row['last_result'] ?? '-';

                return [
                    'title' => $row['title'],
                    'client' => $row['client'] ?: '-',
                    'lawyer' => $row['lawyer'] ?: '-',
                    'specialty' => $row['specialty'] ?: '-',
                    'status' => '<span class="badge bg-' . $statusColor . '">' . $statusLabel . '</span>',
                    'created_at' => Carbon::parse($row['created_at'])->format('d/m/Y'),
                    'follow_ups' => $row['follow_ups_count'],
                    'last_result' => $lastResultLabel,
                    'next_contact' => optional($row['next_contact'])?->format('d/m/Y') ?: '-',
                    'converted' => $row['converted']
                        ? '<span class="badge bg-success">Sí</span>'
                        : '<span class="badge bg-secondary">No</span>',
                    'converted_at' => optional($row['converted_at'])?->format('d/m/Y') ?: '-',
                    'status_raw' => $row['status'],
                    'converted_raw' => $row['converted'],
                ];
            });

        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'summary' => [
                'total_consultations' => $totalConsultations,
                'conversion_rate' => $conversionRate,
                'converted' => $totalConverted,
                'accepted' => $totalAccepted,
                'rejected' => $totalRejected,
                'in_process' => $totalInProcess,
                'total_follow_ups' => $totalFollowUps,
                'avg_follow_ups' => $avgFollowUps,
                'avg_conversion_days' => $avgConversionDays,
            ],
            'charts' => $charts,
            'data' => $rows,
        ]);
    }
}
