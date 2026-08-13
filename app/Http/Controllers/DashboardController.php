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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | Filtro de fechas
        |--------------------------------------------------------------------------
        */

        $dateFrom = $request->filled('date_from')
            ? Carbon::parse($request->date_from)->startOfDay()
            : now()->startOfMonth();

        $dateTo = $request->filled('date_to')
            ? Carbon::parse($request->date_to)->endOfDay()
            : now()->endOfDay();

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
                'prospect'
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
        | LISTA PRÓXIMOS VENCIMIENTOS
        |--------------------------------------------------------------------------
        */

        $upcomingDeadlinesList = AgendaEvent::query()

            ->with(['case.client', 'case.lawyer'])

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
            $upcomingDeadlinesList->whereHas(

                'case',

                function($q) use ($user){

                    $q->where(
                        'lawyer_id',
                        $user->id
                    );

                }

            );
        }

        $upcomingDeadlinesList = $upcomingDeadlinesList
            ->orderBy('start_datetime')
            ->limit(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | LISTA CASOS SIN ACTIVIDAD
        |--------------------------------------------------------------------------
        */

        $inactiveCasesList = CaseFile::query()

            ->with(['client', 'lawyer'])

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
            $inactiveCasesList->where(
                'lawyer_id',
                $user->id
            );
        }

        $inactiveCasesList = $inactiveCasesList
            ->orderBy('opened_at')
            ->limit(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | INGRESOS DEL MES
        |--------------------------------------------------------------------------
        */

        $monthlyIncomeQuery = Payment::query()

            ->whereBetween(
                'payment_date',
                [$dateFrom, $dateTo]
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

            ->whereBetween(
                'expense_date',
                [$dateFrom, $dateTo]
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
        | CONVERSIÓN PERSONAL (ABOGADO)
        |--------------------------------------------------------------------------
        */

        $lawyerConsultations = 0;
        $lawyerConvertedCases = 0;
        $lawyerConversionRate = 0;
        $lawyerProspects = 0;
        $consultationFunnel = [];
        $prospectsToContact = collect();

        if($isLawyer)
        {
            /*
            |--------------------------------------------------------------------------
            | Consultas del abogado
            |--------------------------------------------------------------------------
            */

            $lawyerConsultationsQuery = Consultation::query()
                ->where('lawyer_id', $user->id);

            $lawyerConsultations = (clone $lawyerConsultationsQuery)->count();

            /*
            |--------------------------------------------------------------------------
            | Convertidas a caso (tienen CaseFile)
            |--------------------------------------------------------------------------
            */

            $lawyerConvertedCases = (clone $lawyerConsultationsQuery)
                ->whereHas('case')
                ->count();

            /*
            |--------------------------------------------------------------------------
            | Tasa de conversión
            |--------------------------------------------------------------------------
            */

            $lawyerConversionRate = $lawyerConsultations > 0
                ? round(($lawyerConvertedCases * 100) / $lawyerConsultations, 2)
                : 0;

            /*
            |--------------------------------------------------------------------------
            | Prospectos (en seguimiento)
            |--------------------------------------------------------------------------
            */

            $lawyerProspects = (clone $lawyerConsultationsQuery)
                ->where('status', 'prospect')
                ->count();

            /*
            |--------------------------------------------------------------------------
            | Embudo de consultas por estado
            |--------------------------------------------------------------------------
            */

            $consultationFunnel = (clone $lawyerConsultationsQuery)
                ->select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray();

            /*
            |--------------------------------------------------------------------------
            | Prospectos por contactar (ordenados por próxima fecha)
            |--------------------------------------------------------------------------
            */

            $prospectsToContact = (clone $lawyerConsultationsQuery)
                ->with(['client', 'specialty'])
                ->where('status', 'prospect')
                ->whereNotNull('next_follow_up_at')
                ->orderBy('next_follow_up_at')
                ->limit(5)
                ->get();
        }

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
            ->whereBetween(
                'opened_at',
                [$dateFrom, $dateTo]
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
            ->whereBetween(
                'cases.opened_at',
                [$dateFrom, $dateTo]
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

            'lawyerConsultations' =>
                $lawyerConsultations,

            'lawyerConvertedCases' =>
                $lawyerConvertedCases,

            'lawyerConversionRate' =>
                $lawyerConversionRate,

            'lawyerProspects' =>
                $lawyerProspects,

            'consultationFunnel' =>
                $consultationFunnel,

            'prospectsToContact' =>
                $prospectsToContact,

            'upcomingDeadlinesList' =>
                $upcomingDeadlinesList,

            'inactiveCasesList' =>
                $inactiveCasesList,
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