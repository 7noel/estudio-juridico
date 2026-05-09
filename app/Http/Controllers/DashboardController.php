<?php

namespace App\Http\Controllers;

use App\Models\CaseFile;
use App\Models\Consultation;
use App\Models\AgendaEvent;
use App\Models\CaseActivity;
use App\Models\Document;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $isAdmin =
            $user->hasRole('Administrador')
            || $user->hasRole('Recepcionista');

        /*
        |--------------------------------------------------------------------------
        | CONSULTAS PENDIENTES
        |--------------------------------------------------------------------------
        */

        $consultationsQuery = Consultation::query()
            ->whereIn('status', [
                'new',
                'assigned',
                'evaluating',
                'quoted'
            ]);

        if(!$isAdmin){

            $consultationsQuery->where(
                'assigned_lawyer_id',
                $user->id
            );
        }

        $pendingConsultations =
            $consultationsQuery->count();

        /*
        |--------------------------------------------------------------------------
        | CASOS EN PROCESO
        |--------------------------------------------------------------------------
        */

        $casesQuery = CaseFile::query()
            ->whereIn('status', [
                'open',
                'in_progress'
            ]);

        if(!$isAdmin){

            $casesQuery->where(
                'lawyer_id',
                $user->id
            );
        }

        $casesInProgress = $casesQuery->count();

        /*
        |--------------------------------------------------------------------------
        | EVENTOS HOY
        |--------------------------------------------------------------------------
        */

        $eventsQuery = AgendaEvent::query()
            ->whereDate(
                'start_datetime',
                Carbon::today()
            );

        if(!$isAdmin){

            $eventsQuery->whereHas('case', function($q) use ($user){

                $q->where(
                    'lawyer_id',
                    $user->id
                );

            });

        }

        $todayEvents = $eventsQuery->count();

        /*
        |--------------------------------------------------------------------------
        | PAGOS PENDIENTES
        |--------------------------------------------------------------------------
        */

        $pendingPayments = 0;

        /*
        |--------------------------------------------------------------------------
        | ACTIVIDAD RECIENTE
        |--------------------------------------------------------------------------
        */

        $recentActivities = CaseActivity::query()
            ->with('case')
            ->latest()
            ->limit(10)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | EVENTOS CALENDARIO
        |--------------------------------------------------------------------------
        */

        $calendarEventsQuery = AgendaEvent::query();

        if(!$isAdmin){

            $calendarEventsQuery->whereHas(
                'case',
                function($q) use ($user){

                    $q->where(
                        'lawyer_id',
                        $user->id
                    );

                }
            );

        }

        $calendarEvents = $calendarEventsQuery
            ->get()
            ->map(function($event){

                return [

                    'title' => $event->title,

                    'start' => $event->start_datetime,

                    'end' => $event->end_datetime,

                    'backgroundColor' => '#2563eb',

                    'borderColor' => '#2563eb',

                    'textColor' => '#ffffff',

                    // 🔥 EXTRA
                    'extendedProps' => [

                        'description' => $event->description,

                        'location' => $event->location,

                    ]

                ];

            });

        /*
        |--------------------------------------------------------------------------
        | GRÁFICO CASOS POR ESTADO
        |--------------------------------------------------------------------------
        */

        $casesByStatusQuery = CaseFile::query()
            ->select(
                'status',
                DB::raw('count(*) as total')
            )
            ->groupBy('status');

        if(!$isAdmin){

            $casesByStatusQuery->where(
                'lawyer_id',
                $user->id
            );

        }

        $casesByStatus =
            $casesByStatusQuery->pluck(
                'total',
                'status'
            );

        /*
        |--------------------------------------------------------------------------
        | GRÁFICO ESPECIALIDADES
        |--------------------------------------------------------------------------
        */

        $casesBySpecialtyQuery = CaseFile::query()
            ->join(
                'legal_specialties',
                'legal_specialties.id',
                '=',
                'cases.legal_specialty_id'
            )
            ->select(
                'legal_specialties.name',
                DB::raw('count(*) as total')
            )
            ->groupBy('legal_specialties.name');

        if(!$isAdmin){

            $casesBySpecialtyQuery->where(
                'cases.lawyer_id',
                $user->id
            );

        }

        $casesBySpecialty =
            $casesBySpecialtyQuery->pluck(
                'total',
                'name'
            );

        return view('dashboard.index', [

            'pendingConsultations' =>
                $pendingConsultations,

            'casesInProgress' =>
                $casesInProgress,

            'todayEvents' =>
                $todayEvents,

            'pendingPayments' =>
                $pendingPayments,

            'recentActivities' =>
                $recentActivities,

            'calendarEvents' =>
                $calendarEvents,

            'casesByStatus' =>
                $casesByStatus,

            'casesBySpecialty' =>
                $casesBySpecialty,

        ]);
    }
}