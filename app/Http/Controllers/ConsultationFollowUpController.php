<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConsultationFollowUpRequest;
use App\Models\Consultation;
use App\Models\ConsultationFollowUp;
use Illuminate\Support\Facades\DB;

class ConsultationFollowUpController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort(404);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort(404);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ConsultationFollowUpRequest $request)
    {
        DB::beginTransaction();

        try {

            $consultation = Consultation::findOrFail($request->consultation_id);

            $this->authorize('update', $consultation);

            $consultation->followUps()->create([

                'user_id' => auth()->id(),

                'contact_date' => $request->contact_date,

                'communication_type' => $request->communication_type,

                'result' => $request->result,

                'next_contact_date' => $request->next_contact_date,

                'notes' => $request->notes,

            ]);

            if ($request->result === 'accepted') {

                if ($request->boolean('generate_case')) {

                    $this->createCase($consultation);
                }
            } elseif ($request->result === 'rejected') {

                if ($request->boolean('reject_consultation')) {

                    $this->rejectConsultation($consultation);

                }
            } elseif ($consultation->status === 'new') {

                /*
                |--------------------------------------------------------------------------
                | CAMBIAR A PROSPECTO
                |--------------------------------------------------------------------------
                */

                $consultation->update([
                    'status' => 'prospect',
                    'prospect_at' => now(),
                ]);

            }

            // if ($consultation->status === 'new') {

            //     $consultation->update([
            //         'status' => 'prospect',
            //     ]);

            // }

            $this->refreshFollowUpData($consultation);

            DB::commit();

            return redirect()
                ->route('consultations.show', $consultation)
                ->with('success', 'Seguimiento registrado correctamente.');

        } catch (\Exception $e) {

            DB::rollBack();

            throw $e;

        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ConsultationFollowUp $followUp)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ConsultationFollowUp $followUp)
    {
        abort(404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ConsultationFollowUpRequest $request, ConsultationFollowUp $followUp)
    {
        abort(404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ConsultationFollowUp $followUp)
    {
        $this->authorize('delete', $followUp);

        $consultation = $followUp->consultation;

        $followUp->delete();

        $this->refreshFollowUpData($consultation);

        return response()->json([
            'success' => true,
            'redirect' => route('consultations.show', $consultation),
        ]);
    }


    private function createCase(Consultation $consultation): void
    {
        if ($consultation->case) {
            return;
        }

        CaseFile::create([
            'consultation_id'      => $consultation->id,
            'establishment_id'     => $consultation->establishment_id,
            'client_id'            => $consultation->client_id,
            'service_type'         => $consultation->service_type,
            'legal_specialty_id'   => $consultation->legal_specialty_id,
            'legal_subject_id'     => $consultation->legal_subject_id,
            'lawyer_id'            => $consultation->lawyer_id,
            'title'                => $consultation->title,
            'description'          => $consultation->description,
            'total_amount'         => $consultation->total_amount,
            'status'               => config('options.default_case_status'),
            'opened_at'            => now(),
            'user_id'           => auth()->id(),
        ]);

        $consultation->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);
    }

    private function rejectConsultation(Consultation $consultation): void
    {
        if ($consultation->status === 'accepted') {
            abort(403);
        }

        $consultation->update([
            'status' => 'rejected',
            'rejected_at' => now(),
        ]);
    }

    private function refreshFollowUpData(Consultation $consultation): void
    {
        $lastFollowUp = $consultation->followUps()
            ->latest('contact_date')
            ->latest('id')
            ->first();

        if (!$lastFollowUp) {

            $consultation->update([
                'last_follow_up_at'     => null,
                'last_follow_up_result' => null,
                'next_follow_up_at'     => null,
            ]);

            return;
        }

        $consultation->update([
            'last_follow_up_at'     => $lastFollowUp->contact_date,
            'last_follow_up_result' => $lastFollowUp->result,
            'next_follow_up_at'     => $lastFollowUp->next_contact_date,
        ]);
    }

}