<a href="{{ route('consultations.show', $r->id) }}" class="btn btn-sm btn-outline-primary"> <i class="bi bi-eye"></i> Ver</a>
@if(in_array($r->status, ['rejected']))
    <button type="button" class="btn btn-sm btn-outline-secondary" disabled> <i class="bi bi-pencil"></i> Editar</button>
@else
    <a href="{{ route('consultations.edit', $r->id) }}" class="btn btn-sm btn-outline-secondary"> <i class="bi bi-pencil"></i> Editar</a>

@endif

<!-- <button class="btn btn-sm btn-outline-danger btn-delete" data-id="{{ $r->id }}">Eliminar</button>
<button class="btn btn-sm btn-outline-primary btn-generate-case" data-id="{{ $r->id }}" data-url="{{ route('consultations.generate-case', $r->id) }}">
    Generar caso
</button> -->