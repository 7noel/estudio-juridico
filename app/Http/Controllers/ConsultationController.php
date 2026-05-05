<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\User;
use App\Models\Client;
use App\Models\LegalSpecialty;
use App\Models\CaseFile;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\ConsultationRequest;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    public function index()
    {
        $lawyers = User::role('Abogado')->get();
        return view('consultations.index', compact('lawyers'));
    }

    public function data(Request $request)
    {
        $query = Consultation::with(['client', 'lawyer', 'specialty', 'subject'])
            ->byUser(auth()->user());

        // 🔥 FILTROS

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->lawyer_id) {
            $query->where('lawyer_id', $request->lawyer_id);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        return datatables()->of($query)

            ->addColumn('client', fn($r) => $r->client->full_name ?? '')

            ->addColumn('lawyer', fn($r) => $r->lawyer->name ?? '')

            ->addColumn('service_type', function($r){
                return config('options.service_types')[$r->service_type] ?? '';
            })

            ->addColumn('specialty', fn($r) => $r->specialty->name ?? '')

            ->addColumn('subject', fn($r) => $r->subject->name ?? '')

            // 🔥 STATUS CON COLOR (SOLO UNO, eliminamos duplicado)
            ->addColumn('status', function ($row) {

                $label = config('options.consultation_statuses')[$row->status] ?? $row->status;
                $color = config('options.consultation_status_colors')[$row->status] ?? 'secondary';

                return '<span class="badge bg-' . $color . '">' . $label . '</span>';
            })

            ->editColumn('created_at', function($r){
                return $r->created_at
                    ? $r->created_at->timezone('America/Lima')->format('d/m/Y H:i')
                    : '';
            })

            ->addColumn('actions', function ($r) {
                return view('consultations.partials.actions', compact('r'))->render();
            })

            ->rawColumns(['actions', 'status'])

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

            if (isset($request->installments) and $request->total_amount > 0) {
                $status = 'quoted';
            } elseif ($request->lawyer_id > 0) {
                $status = 'assigned';
            } else {
                $status = config('options.default_consultation_status');
            }
            
            // $status = ($request->lawyer_id > 0) ? 'assigned' : config('options.default_consultation_status') ;

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
                'status' => $status,
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

    public function show(Consultation $consultation)
    {
        $consultation->load([
            'client',
            'installments.payments',
            'case'
        ]);
        $lawyers = User::where('establishment_id', auth()->user()->establishment_id)
            ->role('Abogado')
            ->get();
        $specialties = LegalSpecialty::all();

        return view('consultations.show', compact('consultation', 'lawyers', 'specialties'));
    }

    public function edit(Consultation $consultation)
    {
        $this->authorize('update', $consultation);

        $lawyers = User::where('establishment_id', auth()->user()->establishment_id)
            ->role('Abogado')
            ->get();
        $specialties = LegalSpecialty::all();

        $consultation->load('installments');

        return view('consultations.edit', compact('consultation', 'lawyers', 'specialties'));
    }

    public function update(ConsultationRequest $request, Consultation $consultation)
    {
        $this->authorize('update', $consultation);

        DB::beginTransaction();

        try {
            $oldStatus = $consultation->status;

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

            if (!in_array($consultation->status, ['accepted', 'rejected'])) {

                $consultation->load('installments');

                // 🔹 regla 1: NEW → ASSIGNED
                if ($oldStatus === 'new' && $consultation->lawyer_id) {
                    $newStatus = 'assigned';
                }

                // 🔹 regla 2: → QUOTED
                if (in_array($oldStatus, ['new', 'assigned', 'evaluated']) && $consultation->total_amount > 0 && $consultation->installments->count() > 0) {
                    $newStatus = 'quoted';
                }

                if ($newStatus !== $consultation->status) {
                    $consultation->update([
                        'status' => $newStatus
                    ]);
                }
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

    public function generateCase(Consultation $consultation)
    {
        if (!$consultation->case) {
            //$status = ($consultation->lawyer_id > 0) ? 'assigned' : config('options.default_case_status') ;
            CaseFile::create([
                'consultation_id' => $consultation->id,
                'establishment_id' => $consultation->establishment_id,
                'client_id' => $consultation->client_id,
                'service_type' => $consultation->service_type,
                'legal_specialty_id' => $consultation->legal_specialty_id,
                'legal_subject_id' => $consultation->legal_subject_id,
                'lawyer_id' => $consultation->lawyer_id,
                'title' => $consultation->title,
                'description' => $consultation->description,
                'total_amount' => $consultation->total_amount,
                'status' => config('options.default_case_status'),
                'opened_at' => now(),
                'created_by' => auth()->user()->id,
            ]);

            $consultation->update([
                'status' => 'accepted'
            ]);
        }

        return response()->json(['ok'=>true]);
    }

    public function changeStatus(Request $request, Consultation $consultation)
    {
        $consultation->update([
            'status' => $request->status
        ]);

        return response()->json(['ok' => true]);
    }

    public function reject(Consultation $consultation)
    {
        $this->authorize('update', $consultation);

        if ($consultation->status === 'accepted') {
            abort(403);
        }

        $consultation->update([
            'status' => 'rejected'
        ]);

        return response()->json(['ok' => true]);
    }

    public function stats(Request $request)
    {
        $query = Consultation::query()
            ->with(['client', 'lawyer', 'specialty', 'subject'])
            ->byUser(auth()->user());

        // 🔥 FILTROS

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->lawyer_id) {
            $query->where('lawyer_id', $request->lawyer_id);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // 🔥 BUSCADOR GLOBAL (CLAVE)
        if ($request->search) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('title', 'like', "%{$search}%")

                  ->orWhereHas('client', function ($q2) use ($search) {
                      $q2->where('full_name', 'like', "%{$search}%");
                  })

                  ->orWhereHas('lawyer', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  })

                  ->orWhereHas('specialty', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  })

                  ->orWhereHas('subject', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });

            });
        }

        // 🔥 RESPUESTA FINAL

        return response()->json([
            'all' => (clone $query)->count(),
            'assigned' => (clone $query)->where('status', 'assigned')->count(),
            'quoted' => (clone $query)->where('status', 'quoted')->count(),
            'accepted' => (clone $query)->where('status', 'accepted')->count(),
            'rejected' => (clone $query)->where('status', 'rejected')->count(),
        ]);
    }

}