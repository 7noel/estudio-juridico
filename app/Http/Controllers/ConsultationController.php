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
        $specialties = LegalSpecialty::all();
        return view('consultations.index', compact('lawyers', 'specialties'));
    }

    public function data(Request $request)
    {
        $query = Consultation::with(['client', 'lawyer', 'specialty', 'subject', 'case'])
            ->byUser(auth()->user());

        $workMode = $request->work_mode ?? 'consultations';

        // 🔥 FILTROS
        if ($workMode == 'consultations') {
            $query->applyConsultationFilters($request);

            if ($request->status) {
                $query->where('status', $request->status);
            }

        } else {
            $followUp = $request->follow_up ?? '';
            $query->followUp($followUp);
        }

        $query->select('consultations.*');

        return datatables()->of($query)
            ->addColumn('client', fn($r) => $r->client->full_name ?? '')
            ->addColumn('lawyer', fn($r) => $r->lawyer->name ?? '')
            ->editColumn('service_type', function ($r) {
                return config('options.service_types')[$r->service_type] ?? $r->service_type;
            })
            ->filterColumn('service_type', function ($query, $keyword) {
                $values = collect(config('options.service_types'))
                    ->filter(fn($label) => str_contains(
                        mb_strtolower($label),
                        mb_strtolower($keyword)
                    ))->keys()->toArray();
                if (!empty($values)) {
                    $query->whereIn('service_type', $values);
                }
            })
            ->addColumn('specialty', fn($r) => $r->specialty->name ?? '')
            ->addColumn('subject', fn($r) => $r->subject->name ?? '')
            // 🔥 STATUS CON COLOR (SOLO UNO, eliminamos duplicado)
            ->addColumn('status', function ($row) {
                $label = config('options.consultation_statuses')[$row->status] ?? $row->status;
                $color = config('options.consultation_status_colors')[$row->status] ?? 'secondary';
                if ($row->status == 'prospect') {
                    return '<span class="badge bg-' . $color . '  text-dark">' . $label . '</span>';
                }
                return '<span class="badge bg-' . $color . '">' . $label . '</span>';
            })
            ->editColumn('created_at', function($r){
                return $r->created_at
                    ? $r->created_at->timezone('America/Lima')->format('d/m/Y H:i')
                    : '';
            })
            ->addColumn('case_link', function($row){
                if(!$row->case){
                    return '
                        <span class="badge bg-secondary"> Sin caso </span>
                    ';
                }
                return '
                    <a href="'.route('cases.show', $row->case->id).'" class="badge bg-primary text-decoration-none">
                       Caso #'.$row->case->id.' <i class="bi bi-box-arrow-up-right"></i>
                    </a>
                ';
            })
            ->addColumn('last_follow_up_at', function ($r) {
                return $r->last_follow_up_at ? $r->last_follow_up_at->format('d/m/Y') : '';
            })
            ->editColumn('last_follow_up_result', function ($r) {

                if (!$r->last_follow_up_result) {
                    return '';
                }

                $label = config('options.follow_up_results')[$r->last_follow_up_result] ?? $r->last_follow_up_result;

                $color = config('options.follow_up_result_colors')[$r->last_follow_up_result] ?? 'secondary';

                return '<span class="badge bg-'.$color.'">'.$label.'</span>';

            })
            ->filterColumn('last_follow_up_result', function ($query, $keyword) {
                $values = collect(config('options.follow_up_results'))
                    ->filter(fn($label) => str_contains(
                        mb_strtolower($label),
                        mb_strtolower($keyword)
                    ))->keys()->toArray();
                if (!empty($values)) {
                    $query->whereIn('last_follow_up_result', $values);
                }
            })
            ->addColumn('next_follow_up_at', function ($r) {
                return $r->next_follow_up_at ? $r->next_follow_up_at->format('d/m/Y') : '';
            })
            ->addColumn('actions', function ($r) {
                return view('consultations.partials.actions', compact('r'))->render();
            })
            ->rawColumns(['actions', 'status', 'case_link', 'last_follow_up_result'])
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

            $status = config('options.default_consultation_status');
            
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
                'user_id' => auth()->id(),
            ]);

            foreach ($request->installments ?? [] as $i => $item) {
                $consult->installments()->create([
                    'establishment_id' => $consult->establishment_id,
                    'installment_number' => $i + 1,
                    'amount' => $item['amount'],
                    'paid_amount' => 0,
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

            $data = $request->only(
                'client_id',
                'lawyer_id',
                'title',
                'description',
                'total_amount'
            );

            if ($consultation->status == 'new' and !is_null($request->input('change_to_prospect'))) {
                $data['status'] = 'prospect';
                $data['prospect_at'] = now();
            }

            $consultation->update($data);

            /*
            |--------------------------------------------------------------------------
            | CUOTAS
            |--------------------------------------------------------------------------
            */

            $installments = collect($request->installments ?? [])
                ->values();

            /*
            |--------------------------------------------------------------------------
            | IDS RECIBIDOS
            |--------------------------------------------------------------------------
            */

            $receivedIds = $installments
                ->pluck('id')
                ->filter()
                ->values();

            /*
            |--------------------------------------------------------------------------
            | ELIMINAR SOLO LOS QUE YA NO EXISTEN
            |--------------------------------------------------------------------------
            */

            $installmentsToDelete = $consultation
                ->installments()
                ->whereNotIn('id', $receivedIds)
                ->get();

            foreach($installmentsToDelete as $installment){

                // ======================================
                // NO ELIMINAR SI YA TIENE PAGOS
                // ======================================

                if($installment->payments()->exists()){

                    continue;

                }

                $installment->delete();

            }

            /*
            |--------------------------------------------------------------------------
            | CREAR / ACTUALIZAR
            |--------------------------------------------------------------------------
            */

            foreach ($installments as $i => $item) {

                // ======================================
                // UPDATE
                // ======================================

                if(!empty($item['id'])){

                    $installment = $consultation
                        ->installments()
                        ->where('id', $item['id'])
                        ->first();

                    if($installment){

                        $installment->update([

                            'installment_number' => $i + 1,

                            'amount' => $item['amount'],

                            'due_date' => $item['due_date'],

                        ]);

                    }

                }

                // ======================================
                // CREATE
                // ======================================

                else {

                    $consultation->installments()->create([
                        'establishment_id' => $consultation->establishment_id,

                        'installment_number' => $i + 1,

                        'amount' => $item['amount'],
                        
                        'paid_amount' => 0,

                        'due_date' => $item['due_date'],

                    ]);

                }

            }

            // if (!in_array($consultation->status, ['accepted', 'rejected'])) {

            //     $consultation->load('installments');

            //     // 🔹 regla 1: NEW → ASSIGNED
            //     if ($oldStatus === 'new' && $consultation->lawyer_id) {
            //         $newStatus = 'assigned';
            //     }

            //     // 🔹 regla 2: → QUOTED
            //     if (in_array($oldStatus, ['new', 'assigned', 'evaluated']) && $consultation->total_amount > 0 && $consultation->installments->count() > 0) {
            //         $newStatus = 'quoted';
            //     }

            //     if (isset($newStatus) and $newStatus !== $consultation->status) {
            //         $consultation->update([
            //             'status' => $newStatus
            //         ]);
            //     }
            // }

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
        $this->authorize('update', $consultation);

        $this->createCase($consultation);

        return response()->json(['ok' => true]);
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

        $this->rejectConsultation($consultation);

        return response()->json(['ok' => true]);
    }

    public function stats(Request $request)
    {
        $workMode = $request->work_mode ?? 'consultations';

        if ($workMode === 'consultations') {
            return $this->consultationStats($request);
        }

        return $this->followUpStats($request);
    }

    private function consultationStats(Request $request)
    {
        $query = Consultation::query()
            ->with(['client', 'lawyer', 'specialty', 'subject'])
            ->byUser(auth()->user());

        // 🔥 FILTROS

        $query->applyConsultationFilters($request);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        // 🔥 BUSCADOR GLOBAL (CLAVE)
        if ($request->search) {

            $search = $request->search;

            $serviceTypes = collect(config('options.service_types'))
                ->filter(fn($label) => str_contains(
                    mb_strtolower($label),
                    mb_strtolower($search)
                ))
                ->keys()
                ->toArray();

            $results = collect(config('options.follow_up_results'))
                ->filter(fn($label) => str_contains(
                    mb_strtolower($label),
                    mb_strtolower($search)
                ))
                ->keys()
                ->toArray();

            $query->where(function ($q) use ($search, $serviceTypes, $results) {

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

                if (!empty($serviceTypes)) {
                    $q->orWhereIn('service_type', $serviceTypes);
                }

                if (!empty($results)) {
                    $q->orWhereIn('last_follow_up_result', $results);
                }

            });
        }

        // 🔥 RESPUESTA FINAL

        return response()->json([
            'all' => (clone $query)->count(),
            'new' => (clone $query)->where('status', 'new')->count(),
            'prospect' => (clone $query)->where('status', 'prospect')->count(),
            'accepted' => (clone $query)->where('status', 'accepted')->count(),
            'rejected' => (clone $query)->where('status', 'rejected')->count(),
        ]);
    }

    private function followUpStats(Request $request)
    {
        $query = Consultation::query()
            ->byUser(auth()->user());

        // 🔥 BUSCADOR GLOBAL (CLAVE)
        if ($request->search) {

            $search = $request->search;

            $serviceTypes = collect(config('options.service_types'))
                ->filter(fn($label) => str_contains(
                    mb_strtolower($label),
                    mb_strtolower($search)
                ))
                ->keys()
                ->toArray();

            $results = collect(config('options.follow_up_results'))
                ->filter(fn($label) => str_contains(
                    mb_strtolower($label),
                    mb_strtolower($search)
                ))
                ->keys()
                ->toArray();

            $query->where(function ($q) use ($search, $serviceTypes, $results) {

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

                if (!empty($serviceTypes)) {
                    $q->orWhereIn('service_type', $serviceTypes);
                }

                if (!empty($results)) {
                    $q->orWhereIn('last_follow_up_result', $results);
                }
            });


        }

        return response()->json([

            'all' => (clone $query)->followUp('')->count(),

            'today' => (clone $query)->followUp('today')->count(),

            'overdue' => (clone $query)->followUp('overdue')->count(),

            'week' => (clone $query)->followUp('week')->count(),

            'none' => (clone $query)->followUp('none')->count(),

            'accepted' => (clone $query)->followUp('accepted')->count(),

            'rejected' => (clone $query)->followUp('rejected')->count(),

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
            'user_id'              => auth()->id(),
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

}