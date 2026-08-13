<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ConsultationInstallment;
use App\Models\Establishment;
use App\Models\User;
use Carbon\Carbon;

class CollectionReportController extends Controller
{
    public function index()
    {
        $establishments = Establishment::query()
            ->orderBy('name')
            ->get();

        $lawyers = User::role('Abogado')
            ->orderBy('name')
            ->get();

        return view(
            'reports.collection.index',
            compact(
                'establishments',
                'lawyers'
            )
        );
    }

    public function datatable(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Query base con filtros
        |--------------------------------------------------------------------------
        */

        $query = ConsultationInstallment::query()
            ->with([
                'consultation.client',
                'consultation.lawyer',
                'consultation.establishment',
            ]);

        // Sede
        if ($request->establishment_id) {
            $query->whereHas('consultation', function ($q) use ($request) {
                $q->where('establishment_id', $request->establishment_id);
            });
        }

        // Abogado
        if ($request->lawyer_id) {
            $query->whereHas('consultation', function ($q) use ($request) {
                $q->where('lawyer_id', $request->lawyer_id);
            });
        }

        // Fechas
        if ($request->date_from) {
            $query->whereDate('due_date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('due_date', '<=', $request->date_to);
        }

        // Incluir vencidas anteriores
        if ($request->include_overdue == 1) {
            $query->where(function ($q) use ($request) {
                if ($request->date_from) {
                    $q->whereDate('due_date', '>=', $request->date_from);
                }
                if ($request->date_to) {
                    $q->whereDate('due_date', '<=', $request->date_to);
                }
                $q->orWhere(function ($sub) {
                    $sub->whereDate('due_date', '<', now())
                        ->whereRaw('amount > paid_amount');
                });
            });
        }

        /*
        |--------------------------------------------------------------------------
        | KPIs - Calculados en SQL
        |--------------------------------------------------------------------------
        */

        // Suma de expresión (amount - paid_amount) en SQL
        $pendingQuery = (clone $query)->whereRaw('amount > paid_amount');

        $totalCollected = (clone $query)->sum('paid_amount');

        $totalPending = $pendingQuery
            ->selectRaw('SUM(amount - paid_amount) as total')
            ->value('total') ?? 0;

        $totalPending = round($totalPending, 2);

        $totalOverdue = (clone $query)
            ->whereRaw('amount > paid_amount')
            ->whereDate('due_date', '<', now())
            ->selectRaw('SUM(amount - paid_amount) as total')
            ->value('total') ?? 0;

        $totalOverdue = round($totalOverdue, 2);

        $totalAmount = (clone $query)->sum('amount');

        $collectionEffectiveness = $totalAmount > 0
            ? round(($totalCollected * 100) / $totalAmount, 2)
            : 0;

        $moroseClients = (clone $query)
            ->whereRaw('amount > paid_amount')
            ->whereDate('due_date', '<', now())
            ->whereHas('consultation.client')
            ->selectRaw('COUNT(DISTINCT consultations.client_id) as total')
            ->join('consultations', 'consultation_installments.consultation_id', '=', 'consultations.id')
            ->value('total') ?? 0;

        $avgDaysLate = (clone $query)
            ->whereRaw('amount > paid_amount')
            ->whereDate('due_date', '<', now())
            ->selectRaw('ROUND(AVG(DATEDIFF(NOW(), due_date)), 0) as avg_days')
            ->value('avg_days') ?? 0;

        $avgDaysLate = (int) round($avgDaysLate);

        /*
        |--------------------------------------------------------------------------
        | Estado de cuotas (CASE en SQL)
        |--------------------------------------------------------------------------
        */

        $chartStatus = [
            'paid' => 0,
            'pending' => 0,
            'overdue' => 0,
        ];

        $statusCounts = (clone $query)
            ->selectRaw("
                SUM(CASE WHEN amount <= paid_amount THEN 1 ELSE 0 END) as paid,
                SUM(CASE WHEN amount > paid_amount AND due_date >= NOW() THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN amount > paid_amount AND due_date < NOW() THEN 1 ELSE 0 END) as overdue
            ")
            ->first();

        $chartStatus['paid'] = $statusCounts->paid ?? 0;
        $chartStatus['pending'] = $statusCounts->pending ?? 0;
        $chartStatus['overdue'] = $statusCounts->overdue ?? 0;

        /*
        |--------------------------------------------------------------------------
        | Ingresos por abogado (JOIN + GROUP BY en SQL)
        |--------------------------------------------------------------------------
        */

        $lawyerChartQuery = (clone $query)
            ->selectRaw('COALESCE(users.name, "Sin abogado") as lawyer, SUM(consultation_installments.paid_amount) as total')
            ->join('consultations', 'consultation_installments.consultation_id', '=', 'consultations.id')
            ->leftJoin('users', 'consultations.lawyer_id', '=', 'users.id')
            ->groupBy('consultations.lawyer_id')
            ->get();

        $lawyerChart = [];
        foreach ($lawyerChartQuery as $row) {
            $lawyerChart[] = [
                'lawyer' => $row->lawyer ?? 'Sin abogado',
                'total' => round($row->total, 2),
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Cobranza por sede (JOIN + GROUP BY en SQL)
        |--------------------------------------------------------------------------
        */

        $establishmentChartQuery = (clone $query)
            ->selectRaw('COALESCE(establishments.name, "Sin sede") as establishment, SUM(consultation_installments.paid_amount) as total')
            ->join('consultations', 'consultation_installments.consultation_id', '=', 'consultations.id')
            ->leftJoin('establishments', 'consultations.establishment_id', '=', 'establishments.id')
            ->groupBy('consultations.establishment_id')
            ->get();

        $establishmentChart = [];
        foreach ($establishmentChartQuery as $row) {
            $establishmentChart[] = [
                'establishment' => $row->establishment ?? 'Sin sede',
                'total' => round($row->total, 2),
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Evolución mensual (GROUP BY en SQL)
        |--------------------------------------------------------------------------
        */

        $monthlyChartQuery = (clone $query)
            ->selectRaw('DATE_FORMAT(due_date, "%Y-%m") as month, SUM(paid_amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $monthlyChart = [];
        foreach ($monthlyChartQuery as $row) {
            $monthlyChart[] = [
                'month' => $row->month,
                'total' => round($row->total, 2),
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Antigüedad deuda (CASE en SQL)
        |--------------------------------------------------------------------------
        */

        $agingChartQuery = (clone $query)
            ->whereRaw('amount > paid_amount')
            ->whereDate('due_date', '<', now())
            ->selectRaw("
                SUM(CASE WHEN DATEDIFF(NOW(), due_date) <= 7 THEN amount - paid_amount ELSE 0 END) as '1_7',
                SUM(CASE WHEN DATEDIFF(NOW(), due_date) BETWEEN 8 AND 30 THEN amount - paid_amount ELSE 0 END) as '8_30',
                SUM(CASE WHEN DATEDIFF(NOW(), due_date) BETWEEN 31 AND 60 THEN amount - paid_amount ELSE 0 END) as '31_60',
                SUM(CASE WHEN DATEDIFF(NOW(), due_date) > 60 THEN amount - paid_amount ELSE 0 END) as '60_plus'
            ")
            ->first();

        $agingChart = [
            '1_7' => round($agingChartQuery->{'1_7'} ?? 0, 2),
            '8_30' => round($agingChartQuery->{'8_30'} ?? 0, 2),
            '31_60' => round($agingChartQuery->{'31_60'} ?? 0, 2),
            '60_plus' => round($agingChartQuery->{'60_plus'} ?? 0, 2),
        ];

        /*
        |--------------------------------------------------------------------------
        | Top clientes morosos (GROUP BY en SQL)
        |--------------------------------------------------------------------------
        */

        $topClientsQuery = (clone $query)
            ->whereRaw('amount > paid_amount')
            ->whereDate('due_date', '<', now())
            ->selectRaw('COALESCE(clients.full_name, "Sin cliente") as client, SUM(amount - paid_amount) as debt')
            ->join('consultations', 'consultation_installments.consultation_id', '=', 'consultations.id')
            ->leftJoin('clients', 'consultations.client_id', '=', 'clients.id')
            ->groupBy('consultations.client_id')
            ->orderByDesc('debt')
            ->limit(10)
            ->get();

        $topClients = [];
        foreach ($topClientsQuery as $row) {
            $topClients[] = [
                'client' => $row->client ?? 'Sin cliente',
                'debt' => round($row->debt, 2),
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Ranking abogados (GROUP BY en SQL)
        |--------------------------------------------------------------------------
        */

        $lawyerRankingQuery = (clone $query)
            ->selectRaw("
                COALESCE(users.name, 'Sin abogado') as lawyer,
                SUM(consultation_installments.paid_amount) as collected,
                SUM(CASE WHEN consultation_installments.amount > consultation_installments.paid_amount THEN consultation_installments.amount - consultation_installments.paid_amount ELSE 0 END) as pending
            ")
            ->join('consultations', 'consultation_installments.consultation_id', '=', 'consultations.id')
            ->leftJoin('users', 'consultations.lawyer_id', '=', 'users.id')
            ->groupBy('consultations.lawyer_id')
            ->orderByDesc('collected')
            ->get();

        $lawyerRanking = [];
        foreach ($lawyerRankingQuery as $row) {
            $lawyerRanking[] = [
                'lawyer' => $row->lawyer ?? 'Sin abogado',
                'collected' => round($row->collected, 2),
                'pending' => round($row->pending, 2),
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Filtro de estado en SQL
        |--------------------------------------------------------------------------
        */

        if ($request->status) {
            if ($request->status == 'paid') {
                $query->whereRaw('amount <= paid_amount');
            } elseif ($request->status == 'pending') {
                $query->whereRaw('amount > paid_amount')
                    ->whereDate('due_date', '>=', now());
            } elseif ($request->status == 'overdue') {
                $query->whereRaw('amount > paid_amount')
                    ->whereDate('due_date', '<', now());
            }
        }

        /*
        |--------------------------------------------------------------------------
        | DataTable - registros con estado calculado en PHP (para render)
        |--------------------------------------------------------------------------
        */

        $installments = $query
            ->orderBy('due_date')
            ->get();

        $data = $installments->map(function ($item) {
            $pending = $item->amount - $item->paid_amount;

            if ($pending <= 0) {
                $status = '<span class="badge bg-success">Pagado</span>';
                $statusRaw = 'paid';
            } elseif ($item->due_date < now()) {
                $status = '<span class="badge bg-danger">Vencido</span>';
                $statusRaw = 'overdue';
            } else {
                $status = '<span class="badge bg-warning text-dark">Pendiente</span>';
                $statusRaw = 'pending';
            }

            $daysLate = 0;
            if ($pending > 0 && $item->due_date < now()) {
                $daysLate = Carbon::parse($item->due_date)->diffInDays(now());
            }

            return [
                'client' => $item->consultation->client->full_name ?? '-',
                'consultation' => $item->consultation->title ?? '-',
                'lawyer' => $item->consultation->lawyer->name ?? '-',
                'establishment' => $item->consultation->establishment->name ?? '-',
                'installment' => 'Cuota #' . $item->installment_number,
                'due_date' => optional($item->due_date)?->format('d/m/Y'),
                'days_late' => $daysLate,
                'amount' => 'S/ ' . number_format($item->amount, 2),
                'paid' => 'S/ ' . number_format($item->paid_amount, 2),
                'pending' => 'S/ ' . number_format($pending, 2),
                'status' => $status,
                'status_raw' => $statusRaw,
            ];
        });

        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'data' => $data->values(),
            'records_total' => $installments->count(),
            'summary' => [
                'collected' => number_format($totalCollected, 2),
                'pending' => number_format($totalPending, 2),
                'overdue' => number_format($totalOverdue, 2),
                'installments' => $installments->count(),
                'morose_clients' => $moroseClients,
                'effectiveness' => $collectionEffectiveness,
                'avg_days_late' => $avgDaysLate,
            ],
            'charts' => [
                'status' => $chartStatus,
                'lawyers' => $lawyerChart,
                'establishments' => $establishmentChart,
                'monthly' => $monthlyChart,
                'aging' => $agingChart,
            ],
            'top_clients' => $topClients,
            'lawyer_ranking' => $lawyerRanking,
        ]);
    }
}