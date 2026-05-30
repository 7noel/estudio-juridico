<?php

namespace App\Http\Controllers;

use App\Models\AgendaEvent;
use Illuminate\Http\Request;

class DashboardAgendaController extends Controller
{
    public function events()
    {
        $colors =
            config(
                'options.agenda_event_colors'
            );

        $query = AgendaEvent::query()

            ->whereNull('case_id');

        $user = auth()->user();

        if(
            $user->hasRole('Abogado')
        ){
            $query->where(
                'created_by',
                $user->id
            );
        }

        return $query
            ->get()
            ->map(function ($event) use ($colors) {

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

                    'editable' => true,

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

                        'is_admin_event' =>
                            true,

                    ],

                ];
            });
    }

    public function store(Request $request)
    {
        $request->validate([

            'type' => 'required',

            'title' => 'required',

            'start_datetime' => 'required|date',

            'end_datetime' =>
                'nullable|date|after_or_equal:start_datetime',

        ]);

        $event = AgendaEvent::create([

            'case_id' => null,

            'type' =>
                $request->type,

            'title' =>
                $request->title,

            'description' =>
                $request->description,

            'start_datetime' =>
                $request->start_datetime,

            'end_datetime' =>
                $request->end_datetime,

            'location' =>
                $request->location,

            'created_by' =>
                auth()->id(),

        ]);

        return response()->json([
            'success' => true
        ]);
    }

    public function update(
        Request $request,
        AgendaEvent $event
    )
    {
        abort_if(
            $event->case_id,
            403
        );

        /*
        |--------------------------------------------------------------------------
        | Drag & Drop / Resize
        |--------------------------------------------------------------------------
        */

        if (
            $request->has('start_datetime')
            &&
            !$request->has('title')
        ) {

            $event->update([

                'start_datetime' =>
                    $request->start_datetime,

                'end_datetime' =>
                    $request->end_datetime,

            ]);

            return response()->json([
                'success' => true
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Update Completo
        |--------------------------------------------------------------------------
        */

        $event->update([

            'type' =>
                $request->type,

            'title' =>
                $request->title,

            'description' =>
                $request->description,

            'start_datetime' =>
                $request->start_datetime,

            'end_datetime' =>
                $request->end_datetime,

            'location' =>
                $request->location,

        ]);

        return response()->json([
            'success' => true
        ]);
    }

    public function destroy(AgendaEvent $event)
    {
        abort_if(
            $event->case_id,
            403
        );

        $event->delete();

        return response()->json([
            'success' => true
        ]);
    }

}
