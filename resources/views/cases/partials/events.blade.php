<div class="card mt-3">
    <div class="card-header d-flex justify-content-between">
        <strong>Agenda</strong>

        <button class="btn btn-sm btn-outline-primary" id="btnAddEvent">
            <i class="bi bi-plus"></i>  Agregar
        </button>
    </div>

    <div class="card-body">

        <div id="agenda-list">

            @forelse($case->agendaEvents as $event)
                <div class="border rounded p-2 mb-2">

                    <div class="d-flex justify-content-between">
                        <strong>{{ $event->title }}</strong>

                        <small>
                            {{ $event->start_datetime->format('d/m/Y H:i') }}
                        </small>
                    </div>

                    <div class="text-muted">
                        {{ $event->location }}
                    </div>

                    <div class="mt-1">
                        {{ $event->description }}
                    </div>

                    <div class="text-end mt-2">
                        <button class="btn btn-sm btn-outline-primary btn-edit-event"
                            data-id="{{ $event->id }}"
                            data-title="{{ $event->title }}"
                            data-description="{{ $event->description }}"
                            data-start="{{ $event->start_datetime->format('Y-m-d\TH:i') }}"
                            data-end="{{ optional($event->end_datetime)->format('Y-m-d\TH:i') }}"
                            data-location="{{ $event->location }}">
                            <i class="bi bi-pencil"></i> Editar
                        </button>

                        <button class="btn btn-sm btn-outline-danger btn-delete-event"
                            data-id="{{ $event->id }}">
                            <i class="bi bi-trash"></i> Eliminar
                        </button>
                    </div>

                </div>
            @empty
                <div class="text-muted">No hay eventos</div>
            @endforelse

        </div>

    </div>
</div>

<div class="card mt-3">
    <div class="card-header">
        <strong>Agenda (Calendario)</strong>
    </div>

    <div class="card-body">
        <div id="calendar"></div>
    </div>
</div>

<div class="modal fade" id="modalEvent">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Evento</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <form id="eventForm">

                    <input type="hidden" id="event_id">

                    <div class="mb-2">
                        <label>Título</label>
                        <input type="text" id="event_title" class="form-control">
                    </div>

                    <div class="mb-2">
                        <label>Descripción</label>
                        <textarea id="event_description" class="form-control"></textarea>
                    </div>

                    <div class="mb-2">
                        <label>Inicio</label>
                        <input type="datetime-local" id="event_start" class="form-control">
                    </div>

                    <div class="mb-2">
                        <label>Fin</label>
                        <input type="datetime-local" id="event_end" class="form-control">
                    </div>

                    <div class="mb-2">
                        <label>Lugar</label>
                        <input type="text" id="event_location" class="form-control">
                    </div>

                </form>

            </div>

            <div class="modal-footer">
                <button class="btn btn-primary" id="saveEvent">Guardar</button>
            </div>

        </div>
    </div>
</div>