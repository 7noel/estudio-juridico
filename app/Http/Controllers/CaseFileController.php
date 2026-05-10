<?php

namespace App\Http\Controllers;

use App\Models\CaseFile;
use App\Models\User;
use App\Models\Client;
use App\Models\LegalSpecialty;
use Illuminate\Http\Request;

class CaseFileController extends Controller
{
    public function index()
    {
        $lawyers = User::role('Abogado')->get();
        $specialties = LegalSpecialty::all();

        return view('cases.index', compact('lawyers', 'specialties'));
    }

    public function data(Request $request)
    {
        $query = CaseFile::with(['client', 'lawyer', 'specialty', 'subject', 'consultation'])
            ->forCurrentUser();

        // filtros
        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->lawyer_id) {
            $query->where('lawyer_id', $request->lawyer_id);
        }

        if ($request->service_type) {
            $query->where('service_type', $request->service_type);
        }

        if ($request->legal_specialty_id) {
            $query->where('legal_specialty_id', $request->legal_specialty_id);
        }

        if ($request->date_from) {
            $query->whereDate('opened_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('opened_at', '<=', $request->date_to);
        }

        return datatables()->of($query)

            ->addColumn('client', fn($r) => $r->client->full_name ?? '')
            ->addColumn('lawyer', fn($r) => $r->lawyer->name ?? '')

            ->addColumn('service_type', fn($r) =>
                config('options.service_types')[$r->service_type] ?? ''
            )

            ->addColumn('specialty', fn($r) => $r->specialty->name ?? '')
            ->addColumn('subject', fn($r) => $r->subject->name ?? '')

            ->addColumn('status', function ($row) {
                $label = config('options.case_statuses')[$row->status] ?? $row->status;
                $color = config('options.case_status_colors')[$row->status] ?? 'secondary';
                $text_color = ($row->status == 'in_progress') ? 'text-dark' : '' ;
                return '<span class="badge bg-' . $color . ' ' . $text_color .'">' . $label . '</span>';
            })

            ->editColumn('opened_at', function ($r) {
                return $r->opened_at
                    ? $r->opened_at->timezone('America/Lima')->format('d/m/Y')
                    : '';
            })

            ->addColumn('consultation_link', function($row){
                if(!$row->consultation){
                    return '
                        <span class="badge bg-secondary"> Sin consulta </span>
                    ';
                }
                return '
                    <a href="'.route('consultations.show', $row->consultation->id).'" class="badge bg-primary text-decoration-none">
                        Consulta #'.$row->consultation->id.' <i class="bi bi-box-arrow-up-right"></i> 
                    </a>
                ';
            })
            ->addColumn('actions', function ($r) {
                return view('cases.partials.actions', compact('r'))->render();
            })

            ->rawColumns(['status', 'actions', 'consultation_link'])
            ->make(true);
    }

    public function stats(Request $request)
    {
        $query = CaseFile::query()
            ->forCurrentUser();

        // mismos filtros
        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->lawyer_id) {
            $query->where('lawyer_id', $request->lawyer_id);
        }

        if ($request->service_type) {
            $query->where('service_type', $request->service_type);
        }

        if ($request->legal_specialty_id) {
            $query->where('legal_specialty_id', $request->legal_specialty_id);
        }

        if ($request->date_from) {
            $query->whereDate('opened_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('opened_at', '<=', $request->date_to);
        }

        return response()->json([
            'all' => (clone $query)->count(),
            'open' => (clone $query)->where('status', 'open')->count(),
            'in_progress' => (clone $query)->where('status', 'in_progress')->count(),
            'on_hold' => (clone $query)->where('status', 'on_hold')->count(),
            'closed' => (clone $query)->where('status', 'closed')->count(),
        ]);
    }

    public function show(CaseFile $case)
    {
        $lawyers = User::role('Abogado')->orderBy('name')->get();
        $case->load(['client', 'lawyer', 'specialty', 'subject', 'activities.agendaEvent', 'documents', 'agendaEvents']);

        return view('cases.show', compact('case', 'lawyers'));
    }

    public function changeStatus(Request $request, CaseFile $case)
    {
        $this->authorize('update', $case);

        $request->validate([
            'status' => 'required|in:open,in_progress,on_hold,closed'
        ]);

        $case->update([
            'status' => $request->status
        ]);

        return response()->json(['success' => true]);
    }

    public function quickUpdate(Request $request, CaseFile $case)
    {
        $this->authorize('update', $case);

        $request->validate([

            'case_number' =>
                'nullable|string|max:255',

            'title' =>
                'required|string|max:255',

            'description' =>
                'nullable|string',

            'lawyer_id' =>
                'nullable|exists:users,id',

        ]);

        $data = [

            'case_number' =>
                $request->case_number,

            'title' =>
                $request->title,

            'description' =>
                $request->description,

        ];

        // =====================================
        // SOLO ADMIN PUEDE CAMBIAR ABOGADO
        // =====================================

        if(auth()->user()->hasRole('Administrador')){

            $data['lawyer_id'] =
                $request->lawyer_id;

        }

        $case->update($data);

        return response()->json([
            'success' => true
        ]);
    }

}
