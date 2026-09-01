<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\CaseActivity;
use App\Models\Establishment;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ActivityDetailReportController extends Controller
{
    public function index()
    {
        $establishments = Establishment::orderBy('name')->get();
        $lawyers = User::role('Abogado')->orderBy('name')->get();
        $activityTypes = config('options.activity_main_types');

        return view(
            'reports.activity-detail.index',
            compact(
                'establishments',
                'lawyers',
                'activityTypes'
            )
        );
    }

    public function datatable(Request $request)
    {
        $from = $request->filled('date_start')
            ? Carbon::parse($request->date_start)->startOfDay()
            : now()->startOfDay();

        $to = $request->filled('date_end')
            ? Carbon::parse($request->date_end)->endOfDay()
            : now()->endOfDay();

        $query = CaseActivity::query()
            ->with([
                'case.client',
                'case.lawyer',
                'user',
                'agendaEvent',
            ])
            ->whereBetween('activity_at', [$from, $to]);

        /*
        |--------------------------------------------------------------------------
        | FILTRO ABOGADO (quien realizó la actividad)
        |--------------------------------------------------------------------------
        */

        if ($request->lawyer_id) {
            $query->where('user_id', $request->lawyer_id);
        }

        /*
        |--------------------------------------------------------------------------
        | FILTRO SEDE
        |--------------------------------------------------------------------------
        */

        if ($request->establishment_id) {
            $query->whereHas(
                'case',
                function ($q) use ($request) {
                    $q->where(
                        'establishment_id',
                        $request->establishment_id
                    );
                }
            );
        }

        /*
        |--------------------------------------------------------------------------
        | FILTRO TIPOS DE ACTIVIDAD
        |--------------------------------------------------------------------------
        */

        if ($request->filled('types')) {

            $selectedTypes = $request->types;

            if (in_array('__none__', $selectedTypes, true)) {

                $query->whereRaw('1 = 0');

            } else {

                $query->whereIn('type', $selectedTypes);

            }

        }

        $activities = $query
            ->orderByDesc('activity_at')
            ->orderByDesc('id')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | FILAS
        |--------------------------------------------------------------------------
        */

        $rows = $activities->map(function ($act) {
            return [
                'id' => $act->id,

                'activity_at' =>
                    $act->activity_at?->format('d/m/Y g:i A'),

                'type' => $act->type,

                'type_label' =>
                    config("options.activity_main_types.{$act->type}")
                    ?? $act->type,

                'subtype' => $this->subtypeLabel($act),

                'title' => $act->title ?: '-',

                'description' => $act->description,

                'case_id' => $act->case_id,

                'case_number' =>
                    $act->case?->case_number ?: '-',

                'case_title' =>
                    $act->case?->title ?: '-',

                'client_name' =>
                    $act->case?->client?->full_name ?: '-',

                'case_lawyer' =>
                    $act->case?->lawyer?->name ?: '-',

                'user_name' =>
                    $act->user?->name ?: '-',

                'case_url' =>
                    $act->case
                        ? route('cases.show', $act->case)
                        : null,

                'agenda_event' => $act->agendaEvent ? [
                    'type_label' =>
                        config(
                            "options.agenda_event_types.{$act->agendaEvent->type}"
                        )
                        ?? $act->agendaEvent->type,

                    'title' => $act->agendaEvent->title,

                    'description' =>
                        $act->agendaEvent->description,

                    'start_datetime' =>
                        $act->agendaEvent->start_datetime
                            ?->format('d/m/Y g:i A'),

                    'end_datetime' =>
                        $act->agendaEvent->end_datetime
                            ?->format('d/m/Y g:i A'),

                    'location' => $act->agendaEvent->location,
                ] : null,
            ];
        });

        /*
        |--------------------------------------------------------------------------
        | RESUMEN
        |--------------------------------------------------------------------------
        */

        $byType = [];

        foreach (config('options.activity_main_types') as $key => $label) {
            $byType[$key] = $activities->where('type', $key)->count();
        }

        return response()->json([
            'summary' => [
                'total' => $activities->count(),
                'by_type' => $byType,
            ],
            'data' => $rows->values(),
        ]);
    }

    private function subtypeLabel($act)
    {
        return config("options.activity_types.{$act->subtype}")
            ?? config("options.communication_types.{$act->subtype}")
            ?? config("options.judicial_progress_types.{$act->subtype}")
            ?? ($act->subtype ?: '-');
    }
}
