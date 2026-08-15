<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OwenIt\Auditing\Models\Audit;

class AuditController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:Administrador');
    }

    public function index()
    {
        $entities = $this->entityMap();
        return view('audits.index', compact('entities'));
    }

    public function datatable(Request $request)
    {
        $audits = Audit::query()
            ->with('user')
            ->when($request->entity, fn($q) => $q->where('auditable_type', $request->entity))
            ->when($request->user_id, fn($q) => $q->where('user_id', $request->user_id))
            ->when($request->event, fn($q) => $q->where('event', $request->event))
            ->when($request->date_from, fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('created_at', '<=', $request->date_to));

        $map = $this->entityMap();
        $events = [
            'created' => ['success', 'Creado'],
            'updated' => ['primary', 'Actualizado'],
            'deleted' => ['danger', 'Eliminado'],
            'restored' => ['warning', 'Restaurado'],
        ];

        return datatables()
            ->of($audits)
            ->addColumn('user_name', fn($a) => optional($a->user)->name ?? 'Sistema')
            ->addColumn('entity_label', fn($a) => $map[$a->auditable_type] ?? class_basename($a->auditable_type))
            ->addColumn('event_badge', function ($a) use ($events) {
                [$color, $label] = $events[$a->event] ?? ['secondary', $a->event];
                return '<span class="badge bg-' . $color . '">' . $label . '</span>';
            })
            ->addColumn('changes', function ($a) {
                $changes = [];
                foreach ($a->new_values as $k => $v) {
                    $old = $a->old_values[$k] ?? null;
                    $changes[] = $k . ': ' . ($old !== null ? $old . ' → ' : '') . $v;
                }
                return implode('<br>', $changes);
            })
            ->editColumn('created_at', fn($a) => $a->created_at->format('d/m/Y H:i'))
            ->rawColumns(['event_badge', 'changes'])
            ->make(true);
    }

    private function entityMap()
    {
        return [
            'App\\Models\\AgendaEvent' => 'Agenda',
            'App\\Models\\CaseFile' => 'Casos',
            'App\\Models\\CaseActivity' => 'Actividades',
            'App\\Models\\Client' => 'Clientes',
            'App\\Models\\Consultation' => 'Consultas',
            'App\\Models\\ConsultationFollowUp' => 'Seguimientos',
            'App\\Models\\ConsultationInstallment' => 'Cuotas',
            'App\\Models\\Document' => 'Documentos',
            'App\\Models\\Establishment' => 'Establecimientos',
            'App\\Models\\Expense' => 'Gastos',
            'App\\Models\\LegalSpecialty' => 'Especialidades',
            'App\\Models\\LegalSubject' => 'Materias',
            'App\\Models\\NotificationSetting' => 'Notificaciones',
            'App\\Models\\Payment' => 'Pagos',
            'App\\Models\\User' => 'Usuarios',
        ];
    }
}