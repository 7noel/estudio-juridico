<?php

namespace App\Http\Controllers;


use App\Models\LegalSpecialty;
use App\Models\LegalSubject;
use App\Http\Requests\LegalSpecialtyRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LegalSpecialtyController extends Controller
{
    public function index()
    {
        return view('legal_specialties.index');
    }

    public function data()
    {
        return datatables()->of(
            LegalSpecialty::withCount('subjects')
        )->addColumn('actions', function($r){
            return view('legal_specialties.partials.actions', compact('r'))->render();
        })->rawColumns(['actions'])->make(true);
    }

    public function create()
    {
        return view('legal_specialties.create');
    }

    public function store(LegalSpecialtyRequest $request)
    {
        DB::beginTransaction();
        try {
            $legalSpecialty = LegalSpecialty::create([
                'name' => $request->name
            ]);

            $subjects = collect($request->subjects)
                ->filter(fn($s) => !empty($s['name']))
                ->values(); // 🔥 REINDEXA

            $legalSpecialty->subjects()->delete();

            foreach ($subjects as $s) {
                $legalSpecialty->subjects()->create([
                    'name' => $s['name']
                ]);
            }
            DB::commit();

            return redirect()->route('legal-specialties.index');
        } catch (\Exception $e){
            DB::rollBack();
            throw $e;
        }
    }

    public function edit(LegalSpecialty $legalSpecialty)
    {
        $legalSpecialty->load('subjects');
        return view('legal_specialties.edit', compact('legalSpecialty'));
    }

    public function update(LegalSpecialtyRequest $request, LegalSpecialty $legalSpecialty)
    {
        DB::beginTransaction();
        try {
            $legalSpecialty->update(['name' => $request->name]);
            $subjects = collect($request->subjects)->filter(fn($s) => !empty($s['name']))->values();

            // IDS RECIBIDOS
            $receivedIds = $subjects->pluck('id')->filter()->values();

            // ELIMINAR SOLO LOS QUE YA NO EXISTEN
            $subjectsToDelete = $legalSpecialty->subjects()->whereNotIn('id', $receivedIds)->get();
            foreach($subjectsToDelete as $subject){

                // NO ELIMINAR SI YA TIENE CONSULTAS
                if($subject->consultations()->exists()){
                    continue;
                }
                $subject->delete();
            }
            // CREAR / ACTUALIZAR
            foreach ($subjects as $s) {

                // UPDATE
                if(!empty($s['id'])){
                    $subject = $legalSpecialty->subjects()->where('id', $s['id'])->first();
                    if($subject){
                        $subject->update(['name' => $s['name']]);
                    }
                }

                // CREATE
                else {
                    $legalSpecialty->subjects()->create(['name' => $s['name']]);
                }
            }
            DB::commit();

            return redirect()->route('legal-specialties.index');

        } catch (\Exception $e){
            DB::rollBack();
            throw $e;
        }
    }

    public function destroy(LegalSpecialty $legalSpecialty)
    {
        $legalSpecialty->delete();
        return response()->json(['ok'=>true]);
    }

    public function bySpecialty(Request $request)
    {
        return LegalSubject::where('legal_specialty_id', $request->legal_specialty_id)
            ->get(['id', 'name']);
    }

}
