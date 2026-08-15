@php
    $eventMap = ['created' => ['success','Creado'],'updated' => ['primary','Actualizado'],'deleted' => ['danger','Eliminado'],'restored' => ['warning','Restaurado']];
@endphp
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <h6 class="mb-0"><i class="bi bi-arrow-down-left-square"></i> Cambios en este usuario</h6>
    </div>
    <div class="card-body">
        @if($audits->isEmpty())
            <p class="text-muted mb-0">Sin cambios registrados.</p>
        @else
            <div class="table-responsive">
                <table class="table table-sm table-bordered table-striped">
                    <thead><tr><th>Fecha</th><th>Usuario</th><th>Evento</th><th>Cambios</th></tr></thead>
                    <tbody>
                        @foreach($audits as $a)
                        <tr>
                            <td>{{ $a->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ optional($a->user)->name ?? 'Sistema' }}</td>
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