<div class="card mt-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <strong>Actividades</strong>

        <button class="btn btn-sm btn-outline-primary" id="btnAddActivity"
                data-bs-toggle="modal"
                data-bs-target="#modalActivity">
            <i class="bi bi-plus"></i> Agregar
        </button>
    </div>

    <div class="card-body">

        <div id="activities-list">

            @forelse($case->activities as $act)

                @php
                    $typeLabel = config('options.activity_main_types')[$act->type] ?? $act->type;

                    $subtypeLabel =
                        config('options.activity_types')[$act->subtype]
                        ?? config('options.communication_types')[$act->subtype]
                        ?? 'Otro';

                    $color = match($act->type) {
                        'legal' => 'primary',
                        'communication' => 'info',
                        default => 'secondary'
                    };
                @endphp

                <div class="border rounded p-3 mb-3">

                    {{-- HEADER --}}
                    <div class="d-flex justify-content-between align-items-start">

                        <div>

                            {{-- BADGE + TITULO --}}
                            <div class="d-flex align-items-center gap-2">

                                <span class="badge bg-{{ $color }}">
                                    {{ $typeLabel }}
                                </span>

                                <strong>
                                    {{ $act->title ?? $subtypeLabel }}
                                </strong>

                            </div>

                            {{-- SUBTIPO --}}
                            <small class="text-muted">
                                {{ $subtypeLabel }}
                            </small>

                        </div>

                        {{-- FECHA --}}
                        <small class="text-muted">
                            {{ $act->activity_at?->format('d/m/Y H:i') }}
                        </small>

                    </div>

                    {{-- DESCRIPCION --}}
                    @if($act->description)
                        <div class="mt-2">
                            {{ $act->description }}
                        </div>
                    @endif

                    {{-- FOOTER --}}
                    <div class="text-end mt-2">
                        <div class="text-end mt-2 d-flex justify-content-end gap-2">

                            <button class="btn btn-sm btn-outline-success btn-edit-activity"
                                    data-id="{{ $act->id }}"
                                    data-type="{{ $act->type }}"
                                    data-subtype="{{ $act->subtype }}"
                                    data-title="{{ $act->title }}"
                                    data-description="{{ $act->description }}"
                                    data-date="{{ $act->activity_at?->format('Y-m-d\TH:i') }}">
                                <i class="bi bi-pencil"></i> Editar
                            </button>

                            <button class="btn btn-sm btn-outline-danger btn-delete-activity"
                                    data-id="{{ $act->id }}">
                                <i class="bi bi-trash"></i> Eliminar
                            </button>

                        </div>

                    </div>

                </div>

            @empty
                <div class="text-muted">No hay actividades</div>
            @endforelse

        </div>

    </div>
</div>

<div class="modal fade" id="modalActivity">
    <div class="modal-dialog">
        <div class="modal-content">

            <form id="form-activity">
                @csrf
                <div class="modal-header">
                    <h6 id="modalTitle">Agregar actividad</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">

                    <input type="hidden" id="activity_id" name="activity_id">

                    <div class="mb-2">
                        <label>Tipo</label>
                        <select name="type" id="activity_type" class="form-control">
                            @foreach(config('options.activity_main_types') as $k => $v)
                                <option value="{{ $k }}">{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-2">
                        <label>Subtipo</label>
                        <select name="subtype" id="activity_subtype" class="form-control"></select>
                    </div>

                    <div class="mb-2">
                        <label>Título</label>
                        <input type="text" name="title" class="form-control form-control-sm">
                    </div>

                    <div class="mb-2">
                        <label>Descripción</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div>

                    <div class="mb-2">
                        <label>Fecha</label>
                        <input type="datetime-local" name="activity_at" class="form-control">
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">Guardar</button>
                </div>

            </form>

        </div>
    </div>
</div>
