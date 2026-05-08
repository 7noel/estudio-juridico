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
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_datetime' => 'required|date',
            'end_datetime' => 'nullable|date|after_or_equal:start_datetime',
            'location' => 'nullable|string|max:255',
        ]);

        $case->agendaEvents()->create([
            'title' => $request->title,
            'description' => $request->description,
            'start_datetime' => $request->start_datetime,
            'end_datetime' => $request->end_datetime,
            'location' => $request->location,
            'created_by' => auth()->id(),
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
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_datetime' => 'required|date',
            'end_datetime' => 'nullable|date|after_or_equal:start_datetime',
            'location' => 'nullable|string|max:255',
        ]);

        $event->update($request->only([
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

        return $case->agendaEvents->map(function ($event) {

            $colors = [
                'hearing' => '#dc3545',
                'meeting' => '#0d6efd',
                'call' => '#0dcaf0',
                'document' => '#6c757d',
            ];

            // puedes usar subtype si luego lo agregas
            $color = $colors['default'] ?? '#198754';

            return [
                'id' => $event->id,
                'title' => $event->title,
                'start' => $event->start_datetime,
                'end' => $event->end_datetime,
                'color' => $color,
                'extendedProps' => [
                    'description' => $event->description,
                    'location' => $event->location,
                ],
            ];
        });
    }

}