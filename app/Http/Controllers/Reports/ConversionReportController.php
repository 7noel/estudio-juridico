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
        | Consultas
        |--------------------------------------------------------------------------
        */

        $consultations = Consultation::query()

            ->with([
                'client',
                'lawyer',
                'specialty',
                'subject',
                'case',
                'followUps',
            ]);

        /*
        |--------------------------------------------------------------------------
        | Fecha de creación
        |--------------------------------------------------------------------------
        */

        if ($request->date_from) {

            $consultations->whereDate(
                'created_at',
                '>=',
                $request->date_from
            );

        }

        if ($request->date_to) {

            $consultations->whereDate(
                'created_at',
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

            $consultations->where(
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

            $consultations->where(
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

            $consultations->where(
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

            $consultations->where(
                'status',
                $request->status
            );

        }

        $consultations = $consultations->get();

        /*
        |--------------------------------------------------------------------------
        | Dataset
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

            $followUpsCount =
                $consultation->followUps->count();

            $totalFollowUps +=
                $followUpsCount;

            $hasCase =
                $consultation->case ? true : false;

            if ($hasCase) {

                $totalConverted++;

                if ($consultation->accepted_at) {

                    $conversionDays[] =
                        Carbon::parse(
                            $consultation->created_at
                        )->diffInDays(
                            $consultation->accepted_at
                        );

                }

            }

            if ($consultation->status === 'accepted') {

                $totalAccepted++;

            }

            if ($consultation->status === 'rejected') {

                $totalRejected++;

            }

            if (
                in_array(
                    $consultation->status,
                    ['new', 'prospect']
                )
            ) {

                $totalInProcess++;

            }

            /*
            |--------------------------------------------------------------------------
            | Último seguimiento
            |--------------------------------------------------------------------------
            */

            $lastFollowUp =
                $consultation->followUps
                    ->sortByDesc('contact_date')
                    ->first();

            $dataset[] = [

                'consultation_id' =>
                    $consultation->id,

                'title' =>
                    $consultation->title,

                'client' =>
                    optional(
                        $consultation->client
                    )->full_name,

                'lawyer' =>
                    optional(
                        $consultation->lawyer
                    )->name,

                'specialty' =>
                    optional(
                        $consultation->specialty
                    )->name,

                'status' =>
                    $consultation->status,

                'created_at' =>
                    $consultation->created_at,

                'follow_ups_count' =>
                    $followUpsCount,

                'last_result' =>
                    optional(
                        $lastFollowUp
                    )->result,

                'last_contact' =>
                    optional(
                        $lastFollowUp
                    )->contact_date,

                'next_contact' =>
                    optional(
                        $lastFollowUp
                    )->next_contact_date,

                'converted' =>
                    $hasCase,

                'converted_at' =>
                    $consultation->accepted_at,

            ];

        }

        /*
        |--------------------------------------------------------------------------
        | KPIs
        |--------------------------------------------------------------------------
        */

        $totalConsultations =
            count($dataset);

        $conversionRate =
            $totalConsultations > 0

            ? round(
                ($totalConverted * 100)
                / $totalConsultations,
                2
            )

            : 0;

        $avgFollowUps =
            $totalConsultations > 0

            ? round(
                $totalFollowUps
                / $totalConsultations,
                2
            )

            : 0;

        $avgConversionDays =
            count($conversionDays) > 0

            ? round(
                array_sum($conversionDays)
                / count($conversionDays),
                1
            )

            : 0;

        /*
        |--------------------------------------------------------------------------
        | Embudo por estado
        |--------------------------------------------------------------------------
        */

        $funnel = [];

        foreach ($dataset as $row) {

            $status = $row['status'];

            if (!isset($funnel[$status])) {

                $funnel[$status] = 0;

            }

            $funnel[$status]++;

        }

        /*
        |--------------------------------------------------------------------------
        | Conversión por especialidad
        |--------------------------------------------------------------------------
        */

        $conversionBySpecialty = [];

        foreach ($dataset as $row) {

            $specialty =
                $row['specialty']
                ?: 'Sin especialidad';

            if (!isset(
                $conversionBySpecialty[$specialty]
            )) {

                $conversionBySpecialty[$specialty] = [
                    'total' => 0,
                    'converted' => 0,
                ];

            }

            $conversionBySpecialty[$specialty]['total']++;

            if ($row['converted']) {

                $conversionBySpecialty[$specialty]['converted']++;

            }

        }

        /*
        |--------------------------------------------------------------------------
        | Conversión por abogado
        |--------------------------------------------------------------------------
        */

        $conversionByLawyer = [];

        foreach ($dataset as $row) {

            $lawyer =
                $row['lawyer']
                ?: 'Sin abogado';

            if (!isset(
                $conversionByLawyer[$lawyer]
            )) {

                $conversionByLawyer[$lawyer] = [
                    'total' => 0,
                    'converted' => 0,
                ];

            }

            $conversionByLawyer[$lawyer]['total']++;

            if ($row['converted']) {

                $conversionByLawyer[$lawyer]['converted']++;

            }

        }

        /*
        |--------------------------------------------------------------------------
        | Motivos de rechazo / resultados de seguimiento
        |--------------------------------------------------------------------------
        */

        $followUpResults = [];

        $followUps = ConsultationFollowUp::query()
            ->whereIn(
                'consultation_id',
                $consultations->pluck('id')
            );

        if ($request->date_from) {

            $followUps->whereDate(
                'contact_date',
                '>=',
                $request->date_from
            );

        }

        if ($request->date_to) {

            $followUps->whereDate(
                'contact_date',
                '<=',
                $request->date_to
            );

        }

        $followUps = $followUps->get();

        foreach ($followUps as $followUp) {

            $result =
                $followUp->result
                ?: 'Sin resultado';

            if (!isset(
                $followUpResults[$result]
            )) {

                $followUpResults[$result] = 0;

            }

            $followUpResults[$result]++;

        }

        /*
        |--------------------------------------------------------------------------
        | Canales de comunicación
        |--------------------------------------------------------------------------
        */

        $communicationChannels = [];

        foreach ($followUps as $followUp) {

            $channel =
                $followUp->communication_type
                ?: 'Sin canal';

            if (!isset(
                $communicationChannels[$channel]
            )) {

                $communicationChannels[$channel] = 0;

            }

            $communicationChannels[$channel]++;

        }

        /*
        |--------------------------------------------------------------------------
        | Evolución mensual (consultas vs casos)
        |--------------------------------------------------------------------------
        */

        $monthlyEvolution = [];

        foreach ($dataset as $row) {

            $month =
                Carbon::parse(
                    $row['created_at']
                )->format('Y-m');

            if (!isset(
                $monthlyEvolution[$month]
            )) {

                $monthlyEvolution[$month] = [
                    'consultations' => 0,
                    'converted' => 0,
                ];

            }

            $monthlyEvolution[$month]['consultations']++;

            if ($row['converted']) {

                $monthlyEvolution[$month]['converted']++;

            }

        }

        ksort($monthlyEvolution);

        /*
        |--------------------------------------------------------------------------
        | Charts
        |--------------------------------------------------------------------------
        */

        $charts = [

            'funnel' => [

                'labels' =>
                    array_keys($funnel),

                'values' =>
                    array_values($funnel),

            ],

            'specialties' => [

                'labels' =>
                    array_keys($conversionBySpecialty),

                'converted' =>
                    array_map(
                        fn($v) => $v['converted'],
                        $conversionBySpecialty
                    ),

                'total' =>
                    array_map(
                        fn($v) => $v['total'],
                        $conversionBySpecialty
                    ),

            ],

            'lawyers' => [

                'labels' =>
                    array_keys($conversionByLawyer),

                'converted' =>
                    array_map(
                        fn($v) => $v['converted'],
                        $conversionByLawyer
                    ),

                'total' =>
                    array_map(
                        fn($v) => $v['total'],
                        $conversionByLawyer
                    ),

            ],

            'results' => [

                'labels' =>
                    array_keys($followUpResults),

                'values' =>
                    array_values($followUpResults),

            ],

            'channels' => [

                'labels' =>
                    array_keys($communicationChannels),

                'values' =>
                    array_values($communicationChannels),

            ],

            'monthly' => [

                'labels' =>
                    array_keys($monthlyEvolution),

                'consultations' =>
                    array_map(
                        fn($v) => $v['consultations'],
                        $monthlyEvolution
                    ),

                'converted' =>
                    array_map(
                        fn($v) => $v['converted'],
                        $monthlyEvolution
                    ),

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

                $statusLabel =
                    config(
                        'options.consultation_statuses'
                    )[$row['status']]
                    ?? $row['status'];

                $statusColor =
                    config(
                        'options.consultation_status_colors'
                    )[$row['status']]
                    ?? 'secondary';

                $lastResultLabel =
                    config(
                        'options.follow_up_results'
                    )[$row['last_result']]
                    ?? $row['last_result']
                    ?? '-';

                return [

                    'title' =>
                        $row['title'],

                    'client' =>
                        $row['client']
                        ?: '-',

                    'lawyer' =>
                        $row['lawyer']
                        ?: '-',

                    'specialty' =>
                        $row['specialty']
                        ?: '-',

                    'status' =>
                        '<span class="badge bg-'
                        . $statusColor
                        . '">'
                        . $statusLabel
                        . '</span>',

                    'created_at' =>
                        Carbon::parse(
                            $row['created_at']
                        )->format('d/m/Y'),

                    'follow_ups' =>
                        $row['follow_ups_count'],

                    'last_result' =>
                        $lastResultLabel,

                    'next_contact' =>
                        optional(
                            $row['next_contact']
                        )?->format('d/m/Y')
                        ?: '-',

                    'converted' =>
                        $row['converted']
                            ? '<span class="badge bg-success">Sí</span>'
                            : '<span class="badge bg-secondary">No</span>',

                    'converted_at' =>
                        optional(
                            $row['converted_at']
                        )?->format('d/m/Y')
                        ?: '-',

                    /*
                    |--------------------------------------------------------------------------
                    | Valores crudos
                    |--------------------------------------------------------------------------
                    */

                    'status_raw' =>
                        $row['status'],

                    'converted_raw' =>
                        $row['converted'],

                ];

            });

        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        return response()->json([

            'summary' => [

                'total_consultations' =>
                    $totalConsultations,

                'conversion_rate' =>
                    $conversionRate,

                'converted' =>
                    $totalConverted,

                'accepted' =>
                    $totalAccepted,

                'rejected' =>
                    $totalRejected,

                'in_process' =>
                    $totalInProcess,

                'total_follow_ups' =>
                    $totalFollowUps,

                'avg_follow_ups' =>
                    $avgFollowUps,

                'avg_conversion_days' =>
                    $avgConversionDays,

            ],

            'charts' =>
                $charts,

            'data' =>
                $rows,

        ]);
    }
}