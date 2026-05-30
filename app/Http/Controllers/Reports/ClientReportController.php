<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Client;
use App\Models\CaseFile;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\ConsultationInstallment;
use App\Models\Establishment;
use App\Models\LegalSpecialty;
use App\Models\User;
use App\Models\NotificationSetting;

use Carbon\Carbon;

class ClientReportController extends Controller
{
    public function index()
    {
        $establishments = Establishment::orderBy('name')->get();

        $specialties = LegalSpecialty::orderBy('name')->get();

        $lawyers = User::role('Abogado')
            ->orderBy('name')
            ->get();

        return view(
            'reports.clients.index',
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

                'consultation.installments',

                'consultation.payments',

                'expenses',

                'activities',

            ]);

        /*
        |--------------------------------------------------------------------------
        | Filtros
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

        if ($request->establishment_id) {

            $cases->where(
                'establishment_id',
                $request->establishment_id
            );

        }

        if ($request->specialty_id) {

            $cases->where(
                'legal_specialty_id',
                $request->specialty_id
            );

        }

        if ($request->lawyer_id) {

            $cases->where(
                'lawyer_id',
                $request->lawyer_id
            );

        }

        if ($request->service_type) {

            $cases->where(
                'service_type',
                $request->service_type
            );

        }

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
        | Dataset Clientes
        |--------------------------------------------------------------------------
        */

        $clients = [];

        foreach ($cases as $case) {

            $clientId =
                $case->client_id;

            $clientName =
                optional(
                    $case->client
                )->full_name;

            if (!isset(
                $clients[$clientId]
            )) {

                $clients[$clientId] = [

                    'client_id' =>

                        $clientId,

                    'client_name' =>

                        $clientName,

                    'cases' => 0,

                    'active_cases' => 0,

                    'closed_cases' => 0,

                    'income' => 0,

                    'expense' => 0,

                    'profit' => 0,

                    'debt' => 0,

                    'last_communication' => null,

                    'inactive' => false,

                    'specialties' => [],

                    'service_types' => [],

                    'statuses' => [],

                ];

            }

            /*
            |--------------------------------------------------------------------------
            | Casos
            |--------------------------------------------------------------------------
            */

            $clients[$clientId]['cases']++;

            if (

                in_array(

                    $case->status,

                    [

                        'open',

                        'in_progress'

                    ]

                )

            ) {

                $clients[$clientId]['active_cases']++;

            }

            if (

                $case->status === 'closed'

            ) {

                $clients[$clientId]['closed_cases']++;

            }

            /*
            |--------------------------------------------------------------------------
            | Ingresos
            |--------------------------------------------------------------------------
            */

            $income = optional(
                $case->consultation
            )

                ? $case->consultation
                    ->payments
                    ->sum('amount')

                : 0;

            $clients[$clientId]['income']
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

            $clients[$clientId]['expense']
                +=
                $expense;

            /*
            |--------------------------------------------------------------------------
            | Deuda
            |--------------------------------------------------------------------------
            */

            if ($case->consultation) {

                foreach (

                    $case->consultation
                        ->installments

                    as

                    $installment

                ) {

                    $clients[$clientId]['debt']

                        +=

                        $installment
                            ->pending_amount;

                }

            }

            /*
            |--------------------------------------------------------------------------
            | Especialidades
            |--------------------------------------------------------------------------
            */

            if ($case->specialty) {

                $clients[$clientId]['specialties'][] =
                    $case->specialty->name;

            }

            /*
            |--------------------------------------------------------------------------
            | Tipo Servicio
            |--------------------------------------------------------------------------
            */

            $clients[$clientId]['service_types'][] =
                $case->service_type;

            /*
            |--------------------------------------------------------------------------
            | Estados
            |--------------------------------------------------------------------------
            */

            $clients[$clientId]['statuses'][] =
                $case->status;

            /*
            |--------------------------------------------------------------------------
            | Comunicación
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

            if ($lastCommunication) {

                if (

                    !$clients[$clientId]['last_communication']

                    ||

                    Carbon::parse(
                        $lastCommunication->activity_at
                    )->gt(

                        Carbon::parse(
                            $clients[$clientId]['last_communication']
                        )

                    )

                ) {

                    $clients[$clientId]['last_communication']

                        =

                        $lastCommunication
                            ->activity_at;

                }

            }

        }

        /*
        |--------------------------------------------------------------------------
        | Utilidad e inactividad
        |--------------------------------------------------------------------------
        */

        foreach ($clients as &$client) {

            $client['profit'] =

                $client['income']
                -
                $client['expense'];

            if (

                !$client['last_communication']

            ) {

                $client['inactive'] = true;

            }

            else {

                $days =

                    Carbon::parse(
                        $client['last_communication']
                    )

                    ->diffInDays(
                        now()
                    );

                $client['inactive'] =
                    $days >= $inactiveDays;

            }

        }

        unset($client);

        /*
        |--------------------------------------------------------------------------
        | Dataset Collection
        |--------------------------------------------------------------------------
        */

        $dataset =
            collect($clients);

        /*
        |--------------------------------------------------------------------------
        | KPIs Generales
        |--------------------------------------------------------------------------
        */

        $totalClients =
            $dataset->count();

        $activeClients =
            $dataset

                ->filter(function ($client) {

                    return
                        $client['active_cases'] > 0;

                })

                ->count();

        $clientsWithDebt =
            $dataset

                ->filter(function ($client) {

                    return
                        $client['debt'] > 0;

                })

                ->count();

        $inactiveClients =
            $dataset

                ->filter(function ($client) {

                    return
                        $client['inactive'];

                })

                ->count();

        $totalIncome =
            $dataset->sum(
                'income'
            );

        $totalExpense =
            $dataset->sum(
                'expense'
            );

        $totalProfit =
            $dataset->sum(
                'profit'
            );

        /*
        |--------------------------------------------------------------------------
        | Cliente más rentable
        |--------------------------------------------------------------------------
        */

        $bestClient =
            $dataset

                ->sortByDesc(
                    'profit'
                )

                ->first();

        /*
        |--------------------------------------------------------------------------
        | Cliente con más casos
        |--------------------------------------------------------------------------
        */

        $topCasesClient =
            $dataset

                ->sortByDesc(
                    'cases'
                )

                ->first();

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
        | Especialidades
        |--------------------------------------------------------------------------
        */

        $specialtiesChart = [];

        foreach ($dataset as $client) {

            foreach (

                array_unique(
                    $client['specialties']
                )

                as

                $specialty

            ) {

                if (!isset(
                    $specialtiesChart[$specialty]
                )) {

                    $specialtiesChart[$specialty] = 0;

                }

                $specialtiesChart[$specialty]++;

            }

        }

        /*
        |--------------------------------------------------------------------------
        | Servicios
        |--------------------------------------------------------------------------
        */

        $servicesChart = [];

        foreach ($dataset as $client) {

            foreach (

                array_unique(
                    $client['service_types']
                )

                as

                $service

            ) {

                $label =

                    config(
                        'options.service_types'
                    )[$service]

                    ??

                    $service;

                if (!isset(
                    $servicesChart[$label]
                )) {

                    $servicesChart[$label] = 0;

                }

                $servicesChart[$label]++;

            }

        }

        /*
        |--------------------------------------------------------------------------
        | Estados
        |--------------------------------------------------------------------------
        */

        $statusChart = [];

        foreach ($dataset as $client) {

            foreach (

                array_unique(
                    $client['statuses']
                )

                as

                $status

            ) {

                $label = match ($status) {

                    'open' =>
                        'Abierto',

                    'in_progress' =>
                        'En Proceso',

                    'paused' =>
                        'Pausado',

                    'closed' =>
                        'Cerrado',

                    default =>
                        $status,

                };

                if (!isset(
                    $statusChart[$label]
                )) {

                    $statusChart[$label] = 0;

                }

                $statusChart[$label]++;

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
            | Top ingresos
            |--------------------------------------------------------------------------
            */

            'income' => [

                'labels' =>

                    $rankingIncome

                        ->pluck(
                            'client_name'
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
            | Top utilidad
            |--------------------------------------------------------------------------
            */

            'profit' => [

                'labels' =>

                    $rankingProfit

                        ->pluck(
                            'client_name'
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
            | Especialidades
            |--------------------------------------------------------------------------
            */

            'specialties' => [

                'labels' =>

                    array_keys(
                        $specialtiesChart
                    ),

                'values' =>

                    array_values(
                        $specialtiesChart
                    ),

            ],

            /*
            |--------------------------------------------------------------------------
            | Servicios
            |--------------------------------------------------------------------------
            */

            'services' => [

                'labels' =>

                    array_keys(
                        $servicesChart
                    ),

                'values' =>

                    array_values(
                        $servicesChart
                    ),

            ],

            /*
            |--------------------------------------------------------------------------
            | Estados
            |--------------------------------------------------------------------------
            */

            'status' => [

                'labels' =>

                    array_keys(
                        $statusChart
                    ),

                'values' =>

                    array_values(
                        $statusChart
                    ),

            ],

        ];

        /*
        |--------------------------------------------------------------------------
        | Tabla Principal
        |--------------------------------------------------------------------------
        */

        $rows = $dataset

            ->sortByDesc(
                'profit'
            )

            ->values()

            ->map(function ($client) {

                /*
                |--------------------------------------------------------------------------
                | Última comunicación
                |--------------------------------------------------------------------------
                */

                $lastCommunication =

                    $client['last_communication']

                        ?

                        Carbon::parse(
                            $client['last_communication']
                        )

                        :

                        null;

                /*
                |--------------------------------------------------------------------------
                | Días sin comunicación
                |--------------------------------------------------------------------------
                */

                $daysWithoutCommunication =

                    $lastCommunication

                        ?

                        $lastCommunication
                            ->diffInDays(now())

                        :

                        null;

                return [

                    'client_name' =>

                        $client['client_name'],

                    'cases' =>

                        $client['cases'],

                    'active_cases' =>

                        $client['active_cases'],

                    'closed_cases' =>

                        $client['closed_cases'],

                    'income' =>

                        number_format(
                            $client['income'],
                            2
                        ),

                    'expense' =>

                        number_format(
                            $client['expense'],
                            2
                        ),

                    'profit' =>

                        number_format(
                            $client['profit'],
                            2
                        ),

                    'debt' =>

                        number_format(
                            $client['debt'],
                            2
                        ),

                    'last_communication' =>

                        $lastCommunication

                            ?

                            $lastCommunication
                                ->format(
                                    'd/m/Y H:i'
                                )

                            :

                            'Sin comunicación',

                    'days_without_communication' =>

                        $daysWithoutCommunication
                        ?? '-',

                    /*
                    |--------------------------------------------------------------------------
                    | Valores crudos
                    |--------------------------------------------------------------------------
                    */

                    'income_raw' =>

                        $client['income'],

                    'expense_raw' =>

                        $client['expense'],

                    'profit_raw' =>

                        $client['profit'],

                    'debt_raw' =>

                        $client['debt'],

                ];

            });

        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        return response()->json([

            'summary' => [

                'total_clients' =>

                    $totalClients,

                'active_clients' =>

                    $activeClients,

                'clients_with_debt' =>

                    $clientsWithDebt,

                'inactive_clients' =>

                    $inactiveClients,

                'income' =>

                    round(
                        $totalIncome,
                        2
                    ),

                'expense' =>

                    round(
                        $totalExpense,
                        2
                    ),

                'profit' =>

                    round(
                        $totalProfit,
                        2
                    ),

                'best_client_name' =>

                    $bestClient['client_name']
                    ??
                    '-',

                'best_client_profit' =>

                    round(

                        $bestClient['profit']
                        ??
                        0,

                        2

                    ),

                'top_cases_client_name' =>

                    $topCasesClient['client_name']
                    ??
                    '-',

                'top_cases_client_total' =>

                    $topCasesClient['cases']
                    ??
                    0,

            ],

            'charts' =>

                $charts,

            'data' =>

                $rows,

        ]);

    }

}