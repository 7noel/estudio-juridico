<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CaseFile;
use App\Models\CaseActivity;

class CaseActivityController extends Controller
{
    public function store(Request $request, CaseFile $case)
    {
        $this->authorize('update', $case);

        $request->validate([
            'title' => 'required|string',
            'type' => 'required|in:legal,communication,note',
            'subtype' => 'nullable|string',
            'activity_at' => 'nullable|date',
        ]);

        $case->activities()->create([
            'title' => $request->title 
                ?? config('options.activity_types')[$request->subtype] 
                ?? 'Actividad',
            'type' => $request->type,
            'subtype' => $request->subtype,
            'description' => $request->description,
            'activity_at' => $request->activity_at ?: now(),
            'created_by' => auth()->id(),
        ]);

        return response()->json(['success' => true]);
    }

    public function update(Request $request, CaseActivity $activity)
    {
        $this->authorize('update', $activity);

        $request->validate([
            'type' => 'required',
            'subtype' => 'nullable|string',
            'title' => 'nullable|string',
            'activity_at' => 'nullable|date',
        ]);

        $activity->update([
            'type' => $request->type,
            'subtype' => $request->subtype,
            'title' => $request->title,
            'description' => $request->description,
            'activity_at' => $request->activity_at,
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy(CaseActivity $activity)
    {
        $this->authorize('delete', $activity);

        $activity->delete();

        return response()->json(['success' => true]);
    }
}
