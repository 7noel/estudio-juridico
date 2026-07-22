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
                        <select name="type" id="activity_type" class="form-control form-control-sm">
                            <option value=""> Seleccionar </option>
                            @foreach(config('options.activity_main_types') as $k => $v)
                                <option value="{{ $k }}">{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <label>Subtipo</label>
                        <select name="subtype" id="activity_subtype" class="form-control form-control-sm"></select>
                    </div>
                    <div class="mb-2">
                        <label>Título</label>
                        <input type="text" name="title" class="form-control form-control-sm text-uppercase">
                    </div>
                    <div class="mb-2">
                        <label>Descripción</label>
                        <textarea name="description" class="form-control form-control-sm"></textarea>
                    </div>
                    <div class="mb-2">
                        <label>Fecha</label>
                        <input type="datetime-local" name="activity_at" class="form-control form-control-sm">
                    </div>
                    <hr>
                    <div id="create_agenda_event_wrapper" class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="create_agenda_event" value="1">
                        <label class="form-check-label">
                            Crear evento en agenda
                        </label>
                    </div>
                    <div id="activityAgendaFields" style="display:none;">
                        <div class="row">
                            <div class="alert alert-info py-2 mb-3 d-none" id="activityAgendaLinkedMessage">
                                Esta actividad tiene un evento agenda asociado.
                            </div>
                            <div class="mb-2">
                                <label>Tipo de Evento</label>
                                <select id="activity_event_type" class="form-select form-control-sm">
                                    <option value=""> Seleccionar </option>
                                    @foreach(config('options.agenda_event_types') as $key => $label)
                                        <option value="{{ $key }}"> {{ $label }} </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-2">
                                <label>Título evento</label>
                                <input type="text" id="activity_event_title" class="form-control form-control-sm">
                            </div>
                            <div class="mb-2">
                                <label>Descripción evento</label>
                                <textarea id="activity_event_description" class="form-control form-control-sm" rows="2"></textarea>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label>Fecha inicio</label>
                                <input type="date" id="activity_event_start_date" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label>Hora inicio</label>
                                <select id="activity_event_start_time" class="form-select form-control-sm"> </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label>Fecha fin</label>
                                <input type="date" id="activity_event_end_date" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label>Hora fin</label>
                                <select id="activity_event_end_time" class="form-select form-control-sm"> </select>
                            </div>
                        </div>
                        <div class="mb-2">
                            <label>Lugar</label>
                            <input type="text" id="activity_event_location" class="form-control form-control-sm">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-sm btn-outline-primary"> <i class="bi bi-save"></i> Guardar</button>
                </div>
            </form>

        </div>
    </div>
</div>
