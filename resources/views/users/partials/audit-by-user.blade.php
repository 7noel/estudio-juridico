@php
    $eventMap = ['created' => ['success','Creado'],'updated' => ['primary','Actualizado'],'deleted' => ['danger','Eliminado'],'restored' => ['warning','Restaurado']];
@endphp
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white">
        <h6 class="mb-0"><i class="bi bi-arrow-up-right-square"></i> Actividad realizada por este usuario</h6>
    </div>
    <div class="card-body">
        @if($audits->isEmpty())
            <p class="text-muted mb-0">Sin actividad registrada.</p>
        @else
            <div class="table-responsive">
                <table class="table table-sm table-bordered table-striped">
                    <thead><tr><th>Fecha</th><th>Entidad</th><th>Registro</th><th>Evento</th><th>Cambios</th></tr></thead>
                    <tbody>
                        @foreach($audits as $a)
                        <tr>
                            <td>{{ $a->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ class_basename($a->auditable_type) }}</td>
                            <td>#{{ $a->auditable_id }}</td>
                            <td>@php [$color,$label] = $eventMap[$a->event] ?? ['secondary',$a->event]; @endphp<span class="badge bg-{{ $color }}">{{ $label }}</span></td>
                            <td>
                                @php $changes = $a->getModified(); @endphp
                                @forelse($changes as $field => $chg)
                                    @php
                                        $oldVal = $chg['old'] ?? null;
                                        $newVal = $chg['new'] ?? null;
                                    @endphp
                                    <strong>{{ $field }}</strong>:
                                    @if($oldVal !== null)
                                        {{ $oldVal }} →
                                    @endif
                                    {{ $newVal }}<br>
                                @empty
                                    -
                                @endforelse
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>