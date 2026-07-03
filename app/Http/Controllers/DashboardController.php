<?php

namespace App\Http\Controllers;

use App\Models\CaseFile;
use App\Models\Consultation;
use App\Models\AgendaEvent;
use App\Models\CaseActivity;
use App\Models\Document;
use App\Models\Payment;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | Roles
        |--------------------------------------------------------------------------
        */

        $isAdmin = $user->hasRole('Administrador');

        $isReceptionist = $user->hasRole('Recepcionista');

        $isLawyer = $user->hasRole('Abogado');

        /*
        |--------------------------------------------------------------------------
        | Sedes
        |--------------------------------------------------------------------------
        */

        $canViewAllEstablishments =

            $isAdmin
            ||
            $isReceptionist;

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

        if($isLawyer)
        {
            /*
            |--------------------------------------------------------------------------
            | Dashboard Personal
            |--------------------------------------------------------------------------
            */
            $consultationsQuery->where(
                'lawyer_id',
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

        if($isLawyer)
        {
            /*
            |--------------------------------------------------------------------------
            | Dashboard Personal
            |--------------------------------------------------------------------------
            */
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

        if($isLawyer)
        {
            /*
            |--------------------------------------------------------------------------
            | Dashboard Personal
            |--------------------------------------------------------------------------
            */
            $eventsQuery->whereHas('case', function($q) use ($user){

                $q->where(
                    'lawyer_id',
                    $user->id
                );

            });
        }

        $todayEvents = $eventsQuery->count();

        $upcomingDeadlinesQuery = AgendaEvent::query()

            ->where('type', 'deadline')

            ->whereBetween(

                'start_datetime',

                [
                    now(),
                    now()->copy()->addDays(7)
                ]

            );

        if($isLawyer)
        {
            $upcomingDeadlinesQuery->whereHas(

                'case',

                function($q) use ($user){

                    $q->where(
                        'lawyer_id',
                        $user->id
                    );

                }

            );
        }

        $upcomingDeadlines =
            $upcomingDeadlinesQuery->count();

        $recentInactiveCasesQuery = CaseFile::query()

            ->whereIn('status', [

                'open',

                'in_progress'

            ])

            ->whereDoesntHave(

                'activities',

                function($q){

                    $q->where(

                        'activity_at',

                        '>=',

                        now()->subDays(15)

                    );

                }

            );

        if($isLawyer)
        {
            $recentInactiveCasesQuery->where(
                'lawyer_id',
                $user->id
            );
        }

        $inactiveCases =
            $recentInactiveCasesQuery->count();

        /*
        |--------------------------------------------------------------------------
        | INGRESOS DEL MES
        |--------------------------------------------------------------------------
        */

        $monthlyIncomeQuery = Payment::query()

            ->whereMonth(
                'payment_date',
                now()->month
            )

            ->whereYear(
                'payment_date',
                now()->year
            );

        if($isLawyer)
        {
            $monthlyIncomeQuery->whereHas(

                'consultation',

                function($q) use ($user){

                    $q->where(
                        'lawyer_id',
                        $user->id
                    );

                }

            );
        }

        $monthlyIncome =
            $monthlyIncomeQuery->sum('amount');

        /*
        |--------------------------------------------------------------------------
        | GASTOS DEL MES
        |--------------------------------------------------------------------------
        */

        $monthlyExpenseQuery = Expense::query()

            ->whereMonth(
                'expense_date',
                now()->month
            )

            ->whereYear(
                'expense_date',
                now()->year
            );

        if($isLawyer)
        {
            $monthlyExpenseQuery->whereHas(

                'case',

                function($q) use ($user){

                    $q->where(
                        'lawyer_id',
                        $user->id
                    );

                }

            );
        }

        $monthlyExpense =
            $monthlyExpenseQuery->sum('amount');

        /*
        |--------------------------------------------------------------------------
        | UTILIDAD
        |--------------------------------------------------------------------------
        */

        $monthlyProfit =

            $monthlyIncome
            -
            $monthlyExpense;

        /*
        |--------------------------------------------------------------------------
        | PAGOS PENDIENTES
        |--------------------------------------------------------------------------
        */

        $consultationsPaymentsQuery =
            Consultation::query();

        if($isLawyer)
        {
            /*
            |--------------------------------------------------------------------------
            | Dashboard Personal
            |--------------------------------------------------------------------------
            */
            $consultationsPaymentsQuery->where(
                'lawyer_id',
                $user->id
            );
        }

        $totalConsultations =
            $consultationsPaymentsQuery
                ->sum('total_amount');

        /*
        |--------------------------------------------------------------------------
        | PAGOS REALIZADOS
        |--------------------------------------------------------------------------
        */

        $paymentsQuery = Payment::query()
            ->whereHas('consultation');

        if($isLawyer)
        {
            /*
            |--------------------------------------------------------------------------
            | Dashboard Personal
            |--------------------------------------------------------------------------
            */
            $paymentsQuery->whereHas(
                'consultation',
                function($q) use ($user){

                    $q->where(
                        'lawyer_id',
                        $user->id
                    );

                }
            );
        }

        $totalPaid =
            $paymentsQuery->sum('amount');

        /*
        |--------------------------------------------------------------------------
        | PENDIENTE
        |--------------------------------------------------------------------------
        */

        $pendingPayments =
            max(
                $totalConsultations - $totalPaid,
                0
            );

        /*
        |--------------------------------------------------------------------------
        | ACTIVIDAD RECIENTE
        |--------------------------------------------------------------------------
        */

        $recentActivities = CaseActivity::query()

            ->with('case');

        if($isLawyer)
        {
            $recentActivities->whereHas(

                'case',

                function($q) use ($user){

                    $q->where(
                        'lawyer_id',
                        $user->id
                    );

                }

            );
        }

        $recentActivities =

            $recentActivities

                ->latest('activity_at')

                ->limit(10)

                ->get();

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

        if($isLawyer)
        {
            /*
            |--------------------------------------------------------------------------
            | Dashboard Personal
            |--------------------------------------------------------------------------
            */
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

        if($isLawyer)
        {
            /*
            |--------------------------------------------------------------------------
            | Dashboard Personal
            |--------------------------------------------------------------------------
            */
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

            'casesByStatus' =>
                $casesByStatus,

            'casesBySpecialty' =>
                $casesBySpecialty,

            'isAdmin' => $isAdmin,

            'isReceptionist' => $isReceptionist,

            'isLawyer' => $isLawyer,

            'canViewAllEstablishments'
                => $canViewAllEstablishments,

            'upcomingDeadlines' =>
                $upcomingDeadlines,

            'inactiveCases' =>
                $inactiveCases,

            'monthlyIncome' =>
                $monthlyIncome,

            'monthlyExpense' =>
                $monthlyExpense,

            'monthlyProfit' =>
                $monthlyProfit,
        ]);
    }


    public function calendarEvents()
    {
        $user = auth()->user();

        $colors =
            config(
                'options.agenda_event_colors'
            );

        $query = AgendaEvent::query()

            ->with([
                'case.client',
                'case.lawyer'
            ])

            ->whereNotNull('case_id');

        /*
        |--------------------------------------------------------------------------
        | ABOGADO
        |--------------------------------------------------------------------------
        */

        if($user->hasRole('Abogado'))
        {
            $query->whereHas(

                'case',

                function($q) use ($user){

                    $q->where(
                        'lawyer_id',
                        $user->id
                    );

                }

            );
        }

        return $query
            ->get()
            ->map(function($event) use ($colors){

                $style =

                    $colors[$event->type]

                    ??

                    [

                        'background' => '#6c757d',

                        'text' => '#ffffff'

                    ];

                return [

                    'id' => $event->id,

                    'title' => $event->title,

                    'start' => $event->start_datetime,

                    'end' => $event->end_datetime,

                    'backgroundColor' =>
                        $style['background'],

                    'borderColor' =>
                        $style['background'],

                    'textColor' =>
                        $style['text'],

                    'editable' => false,

                    'extendedProps' => [

                        'type' =>
                            $event->type,

                        'type_label' =>
                            config(
                                'options.agenda_event_types'
                            )[$event->type]

                            ??

                            'Otro',

                        'description' =>
                            $event->description,

                        'location' =>
                            $event->location,

                        'case_id' =>
                            $event->case_id,

                        'case_url' => route(
                            'cases.show',
                            $event->case_id
                        ),

                        'client_name' =>
                            optional(
                                optional($event->case)->client
                            )->full_name,

                        'case_title' =>
                            optional($event->case)
                                ->title,

                        'lawyer_name' =>
                            optional(
                                optional($event->case)
                                    ->lawyer
                            )->name,

                        'is_legal_event' =>
                            true,

                    ],

                ];

            });

    }


}