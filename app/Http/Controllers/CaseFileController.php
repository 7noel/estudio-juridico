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
        $query = CaseFile::with(['client', 'lawyer', 'specialty', 'subject'])
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

                return '<span class="badge bg-' . $color . '">' . $label . '</span>';
            })

            ->editColumn('opened_at', function ($r) {
                return $r->opened_at
                    ? $r->opened_at->timezone('America/Lima')->format('d/m/Y')
                    : '';
            })

            ->addColumn('actions', function ($r) {
                return view('cases.partials.actions', compact('r'))->render();
            })

            ->rawColumns(['status', 'actions'])
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
        $case->load(['client', 'lawyer', 'specialty', 'subject']);

        return view('cases.show', compact('case'));
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

}
