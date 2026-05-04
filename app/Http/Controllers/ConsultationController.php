<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\User;
use App\Models\Client;
use App\Models\LegalSpecialty;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\ConsultationRequest;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    public function index()
    {
        return view('consultations.index');
    }

    public function data()
    {
        $query = Consultation::with(['client', 'lawyer', 'specialty', 'subject'])
            ->byUser(auth()->user());

        return datatables()->of($query)
            ->addColumn('client', fn($r) => $r->client->full_name ?? '')
            ->addColumn('lawyer', fn($r) => $r->lawyer->name ?? '')
            ->addColumn('service_type', function($r){
                return config('options.service_types')[$r->service_type] ?? '';
            })
            ->addColumn('specialty', fn($r) => $r->specialty->name ?? '')
            ->addColumn('subject', fn($r) => $r->subject->name ?? '')
            ->addColumn('status', function($r){
                return config('options.consultation_statuses')[$r->status] ?? '';
            })
            ->editColumn('created_at', function($r){
                return $r->created_at
                    ? $r->created_at->timezone('America/Lima')->format('d/m/Y H:i')
                    : '';
            })
            ->addColumn('actions', function ($r) {
                return view('consultations.partials.actions', compact('r'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function create()
    {
        $lawyers = User::where('establishment_id', auth()->user()->establishment_id)
            ->role('Abogado')
            ->get();
        $specialties = LegalSpecialty::all();
        //dd($specialties);

        return view('consultations.create', compact('lawyers', 'specialties'));
    }

    public function store(ConsultationRequest $request)
    {
        DB::beginTransaction();

        try {
            $consult = Consultation::create([
                'establishment_id' => auth()->user()->establishment_id,
                'service_type' => $request->service_type,
                'legal_specialty_id' => $request->legal_specialty_id,
                'legal_subject_id' => $request->legal_subject_id,
                'client_id' => $request->client_id,
                'lawyer_id' => $request->lawyer_id,
                'title' => $request->title,
                'description' => $request->description,
                'total_amount' => $request->total_amount,
                'status' => 'registered',
                'created_by' => auth()->id(),
            ]);

            foreach ($request->installments ?? [] as $i => $item) {
                $consult->installments()->create([
                    'installment_number' => $i + 1,
                    'amount' => $item['amount'],
                    'due_date' => $item['due_date'],
                ]);
            }

            DB::commit();

            return redirect()->route('consultations.index');

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function edit(Consultation $consultation)
    {
        $this->authorize('update', $consultation);

        $lawyers = User::where('establishment_id', auth()->user()->establishment_id)
            ->role('Abogado')
            ->get();
        $specialties = LegalSpecialty::pluck('name','id');

        $consultation->load('installments', 'specialties');

        return view('consultations.edit', compact('consultation', 'lawyers', 'specialties'));
    }

    public function update(ConsultationRequest $request, Consultation $consultation)
    {
        $this->authorize('update', $consultation);

        DB::beginTransaction();

        try {
            $consultation->update($request->only(
                'client_id',
                'lawyer_id',
                'title',
                'description',
                'total_amount'
            ));

            $consultation->installments()->delete();

            foreach ($request->installments ?? [] as $i => $item) {
                $consultation->installments()->create([
                    'installment_number' => $i + 1,
                    'amount' => $item['amount'],
                    'due_date' => $item['due_date'],
                ]);
            }

            DB::commit();

            return redirect()->route('consultations.index');

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function destroy(Consultation $consultation)
    {
        $this->authorize('delete', $consultation);

        $consultation->delete();

        return response()->json(['success' => true]);
    }
}