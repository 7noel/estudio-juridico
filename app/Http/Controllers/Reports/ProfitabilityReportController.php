<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\CaseFile;
use App\Models\Establishment;
use App\Models\LegalSpecialty;
use App\Models\User;

class ProfitabilityReportController extends Controller
{
    public function index()
    {
        $establishments = Establishment::orderBy('name')->get();
        $specialties = LegalSpecialty::orderBy('name')->get();
        $lawyers = User::orderBy('name')->get();

        return view(
            'reports.profitability.index',
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
        | Query base con filtros
        |--------------------------------------------------------------------------
        */

        $cases = CaseFile::query()
            ->with([
                'client',
                'lawyer',
                'specialty',
                'establishment',
                'consultation.payments',
                'expenses',
            ]);

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

        // Servicio
        if ($request->service_type) {
            $cases->where('service_type', $request->service_type);
        }

        // Estado
        if ($request->status) {
            $cases->where('status', $request->status);
        }

        $cases = $cases->get();

        /*
        |--------------------------------------------------------------------------
        | Dataset principal - usando withSum para evitar N+1
        |--------------------------------------------------------------------------
        */

        $dataset = $cases->map(function ($case) {
            $income = $case->consultation ? $case->consultation->payments->sum('amount') : 0;
            $expense = $case->expenses->sum('amount');
            $profit = $income - $expense;
            $margin = $income > 0 ? round(($profit * 100) / $income, 2) : 0;

            return [
                'case_id' => $case->id,
                'case_title' => $case->title,
                'client' => optional($case->client)->full_name,
                'lawyer' => optional($case->lawyer)->name,
                'specialty' => optional($case->specialty)->name,
                'service_type' => config('options.service_types')[$case->service_type] ?? $case->service_type,
                'status' => $case->status,
                'income' => round($income, 2),
                'expense' => round($expense, 2),
                'profit' => round($profit, 2),
                'margin' => $margin,
            ];
        });

        /*
        |--------------------------------------------------------------------------
        | KPIs - usando colección de datos ya calculados
        |--------------------------------------------------------------------------
        */

        $totalProfit = $dataset->sum('profit');
        $profitableCases = $dataset->where('profit', '>', 0)->count();
        $lossCases = $dataset->where('profit', '<', 0)->count();
        $avgProfit = count($dataset) ? round($totalProfit / count($dataset), 2) : 0;

        /*
        |--------------------------------------------------------------------------
        | Caso más rentable
        |--------------------------------------------------------------------------
        */

        $bestCase = $dataset->sortByDesc('profit')->first();

        /*
        |--------------------------------------------------------------------------
        | Cliente más rentable (desde colección PHP)
        |--------------------------------------------------------------------------
        */

        $clientsProfit = $dataset->groupBy('client')->map(function ($items) {
            return $items->sum('profit');
        });

        $bestClientName = $clientsProfit->sortDesc()->keys()->first() ?? '-';
        $bestClientProfit = round($clientsProfit->sortDesc()->first() ?? 0, 2);

        /*
        |--------------------------------------------------------------------------
        | Top casos rentables
        |--------------------------------------------------------------------------
        */

        $topCases = $dataset->sortByDesc('profit')->take(10)->values();

        /*
        |--------------------------------------------------------------------------
        | Utilidad por abogado (agrupado en PHP)
        |--------------------------------------------------------------------------
        */

        $lawyerProfit = $dataset->groupBy('lawyer')->map(function ($items) {
            return round($items->sum('profit'), 2);
        })->toArray();

        /*
        |--------------------------------------------------------------------------
        | Utilidad por especialidad (agrupado en PHP)
        |--------------------------------------------------------------------------
        */

        $specialtyProfit = $dataset->groupBy('specialty')->map(function ($items) {
            return round($items->sum('profit'), 2);
        })->toArray();

        /*
        |--------------------------------------------------------------------------
        | Utilidad por servicio (agrupado en PHP)
        |--------------------------------------------------------------------------
        */

        $serviceProfit = $dataset->groupBy('service_type')->map(function ($items) {
            return round($items->sum('profit'), 2);
        })->toArray();

        /*
        |--------------------------------------------------------------------------
        | Charts
        |--------------------------------------------------------------------------
        */

        $charts = [
            'top_cases' => [
                'labels' => $topCases->pluck('case_title')->values(),
                'values' => $topCases->pluck('profit')->values(),
            ],
            'lawyers' => [
                'labels' => array_keys($lawyerProfit),
                'values' => array_values($lawyerProfit),
            ],
            'specialties' => [
                'labels' => array_keys($specialtyProfit),
                'values' => array_values($specialtyProfit),
            ],
            'services' => [
                'labels' => array_keys($serviceProfit),
                'values' => array_values($serviceProfit),
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
            ->map(function ($row) {
                return [
                    'case_title' => $row['case_title'],
                    'client' => $row['client'],
                    'specialty' => $row['specialty'],
                    'lawyer' => $row['lawyer'],
                    'service_type' => $row['service_type'],
                    'status' => $row['status'],
                    'income' => number_format($row['income'], 2),
                    'expense' => number_format($row['expense'], 2),
                    'profit' => number_format($row['profit'], 2),
                    'margin' => number_format($row['margin'], 2) . '%',
                    'income_raw' => $row['income'],
                    'expense_raw' => $row['expense'],
                    'profit_raw' => $row['profit'],
                    'margin_raw' => $row['margin'],
                ];
            });

        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'summary' => [
                'total_profit' => round($totalProfit, 2),
                'profitable_cases' => $profitableCases,
                'loss_cases' => $lossCases,
                'avg_profit' => round($avgProfit, 2),
                'best_case_title' => $bestCase['case_title'] ?? '-',
                'best_case_profit' => $bestCase['profit'] ?? 0,
                'best_client_name' => $bestClientName,
                'best_client_profit' => $bestClientProfit,
            ],
            'charts' => $charts,
            'data' => $rows,
        ]);
    }
}
