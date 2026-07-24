<?php

namespace App\Http\Controllers;

use App\Models\AgendaEvent;
use App\Models\CaseFile;
use Illuminate\Http\Request;

class AgendaEventController extends Controller
{
    public function store(Request $request, CaseFile $case)
    {
        $this->authorize('update', $case);

        $request->validate([
            'type' => 'required|string',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_datetime' => 'required|date',
            'end_datetime' => 'nullable|date|after:start_datetime',
            'location' => 'nullable|string|max:255',
        ]);

        $case->agendaEvents()->create([
            'type' => $request->type,
            'title' => $request->title,
            'description' => $request->description,
            'start_datetime' => $request->start_datetime,
            'end_datetime' => $request->end_datetime,
            'location' => $request->location,
            'user_id' => auth()->id(),
        ]);

        return response()->json(['success' => true]);
    }

    public function update(Request $request, AgendaEvent $event)
    {
        $this->authorize('update', $event->case);

        // 🔥 SI SOLO VIENE FECHA (drag/drop)
        if ($request->has('start_datetime') && !$request->has('title')) {

            $request->validate([
                'start_datetime' => 'required|date',
                'end_datetime' => 'nullable|date|after_or_equal:start_datetime',
            ]);

            $event->update([
                'start_datetime' => $request->start_datetime,
                'end_datetime' => $request->end_datetime,
            ]);

            return response()->json(['success' => true]);
        }
        
        // 🔥 UPDATE COMPLETO (modal)
        $request->validate([
            'type' => 'required|string',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_datetime' => 'required|date',
            'end_datetime' => 'nullable|date|after_or_equal:start_datetime',
            'location' => 'nullable|string|max:255',
        ]);

        $event->update($request->only([
            'type',
            'title',
            'description',
            'start_datetime',
            'end_datetime',
            'location',
        ]));

        return response()->json(['success' => true]);
    }

    public function destroy(AgendaEvent $event)
    {
        $this->authorize('update', $event->case);

        $event->delete();

        return response()->json(['success' => true]);
    }

    public function events(CaseFile $case)
    {
        $this->authorize('view', $case);

        $colors = config('options.agenda_event_colors');

        return $case->agendaEvents->map(function ($event) use ($colors) {

            $style = $colors[$event->type] ?? [

                'background' => '#6c757d',
                'text' => '#ffffff',

            ];

            return [
                'id' => $event->id,
                'title' => $event->title,
                'start' => $event->start_datetime,
                'end' => $event->end_datetime,
                'backgroundColor' => $style['background'],
                'borderColor' => $style['background'],
                'textColor' => $style['text'],
                'extendedProps' => [
                    'type' => $event->type,
                    'type_label' => config('options.agenda_event_types')[$event->type] ?? 'Otro',
                    'description' => $event->description,
                    'location' => $event->location,
                ],
            ];
        });
    }

}