<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CaseFile;
use App\Models\CaseActivity;
use Illuminate\Support\Facades\DB;

class CaseActivityController extends Controller
{
    public function store(Request $request, CaseFile $case)
    {
        $this->authorize('update', $case);

        $request->validate([
            'type' => 'required|in:legal,judicial_progress,communication,note',

            'subtype' => 'nullable|string',

            'title' => 'nullable|string|max:255',

            'description' => 'required|string',

            'activity_at' => 'nullable|date',

            // Agenda
            'create_agenda_event' => 'nullable|boolean',

            'agenda_type' => 'required_if:create_agenda_event,1|nullable|string',

            'agenda_title' => 'required_if:create_agenda_event,1|nullable|string|max:255',

            'agenda_description' => 'nullable|string',

            'agenda_start_datetime' => [
                'exclude_unless:create_agenda_event,1',
                'required',
                'date',
            ],

            'agenda_end_datetime' => [
                'exclude_unless:create_agenda_event,1',
                'required',
                'date',
                'after:agenda_start_datetime',
            ],

            'agenda_location' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {

            // ======================================
            // ACTIVIDAD
            // ======================================

            $activity = $case->activities()->create([

                'title' =>
                    $request->title
                    ?? config('options.activity_types')[$request->subtype]
                    ?? 'Actividad',

                'type' => $request->type,

                'subtype' => $request->subtype,

                'description' => $request->description,

                'activity_at' =>
                    $request->activity_at ?: now(),

                'user_id' => auth()->id(),

            ]);

            // ======================================
            // AGENDA
            // ======================================

            if($request->create_agenda_event){

                $case->agendaEvents()->create([

                    'case_activity_id' => $activity->id,

                    'type' => $request->agenda_type,

                    'title' => $request->agenda_title,

                    'description' => $request->agenda_description,

                    'start_datetime' =>
                        $request->agenda_start_datetime,

                    'end_datetime' =>
                        $request->agenda_end_datetime,

                    'location' =>
                        $request->agenda_location,

                    'user_id' => auth()->id(),

                ]);

            }

            DB::commit();

            return response()->json([
                'success' => true
            ]);

        } catch (\Exception $e){

            DB::rollBack();

            throw $e;
        }
    }

    public function update(Request $request, CaseActivity $activity)
    {
        $this->authorize('update', $activity);

        $request->validate([
            'type' => 'required|in:legal,judicial_progress,communication,note',

            'subtype' => 'nullable|string',

            'title' => 'nullable|string|max:255',

            'description' => 'required|string',

            'activity_at' => 'nullable|date',

            // Agenda
            'create_agenda_event' => 'nullable|boolean',

            'agenda_type' => 'required_if:create_agenda_event,1|nullable|string',

            'agenda_title' => 'required_if:create_agenda_event,1|nullable|string|max:255',

            'agenda_description' => 'nullable|string',

            'agenda_start_datetime' => [
                'exclude_unless:create_agenda_event,1',
                'required',
                'date',
            ],

            'agenda_end_datetime' => [
                'exclude_unless:create_agenda_event,1',
                'required',
                'date',
                'after:agenda_start_datetime',
            ],

            'agenda_location' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {

            // ======================================
            // ACTUALIZAR ACTIVIDAD
            // ======================================

            $activity->update([

                'type' => $request->type,

                'subtype' => $request->subtype,

                'title' => $request->title,

                'description' => $request->description,

                'activity_at' => $request->activity_at,

            ]);

            // ======================================
            // EVENTO RELACIONADO
            // ======================================

            $event = $activity->agendaEvent;

            // ======================================
            // SI YA EXISTE EVENTO → ACTUALIZAR
            // ======================================

            if($event){

                $event->update([

                    'type' => $request->agenda_type,

                    'title' =>
                        $request->agenda_title,

                    'description' =>
                        $request->agenda_description,

                    'start_datetime' =>
                        $request->agenda_start_datetime,

                    'end_datetime' =>
                        $request->agenda_end_datetime,

                    'location' =>
                        $request->agenda_location,

                ]);

            }

            // ======================================
            // SI NO EXISTE Y ACTIVARON SWITCH
            // → CREAR EVENTO
            // ======================================

            elseif($request->create_agenda_event){

                $activity->case->agendaEvents()->create([

                    'case_activity_id' =>
                        $activity->id,

                    'type' => $request->agenda_type,

                    'title' =>
                        $request->agenda_title,

                    'description' =>
                        $request->agenda_description,

                    'start_datetime' =>
                        $request->agenda_start_datetime,

                    'end_datetime' =>
                        $request->agenda_end_datetime,

                    'location' =>
                        $request->agenda_location,

                    'user_id' =>
                        auth()->id(),

                ]);

            }

            DB::commit();

            return response()->json([
                'success' => true
            ]);

        } catch (\Exception $e){

            DB::rollBack();

            throw $e;
        }
    }

    public function destroy(CaseActivity $activity)
    {
        $this->authorize('delete', $activity);

        DB::beginTransaction();

        try {

            if($activity->agendaEvent){

                $activity->agendaEvent->delete();

            }

            $activity->delete();

            DB::commit();

            return response()->json([
                'success' => true
            ]);

        } catch (\Exception $e){

            DB::rollBack();

            throw $e;
        }
    }

}
