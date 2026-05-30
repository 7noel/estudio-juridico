<div class="card shadow-sm">

    <div class="card-header d-flex justify-content-between align-items-center">

        <h5 class="mb-0">

            Agenda

        </h5>

        <small class="text-muted">

            Eventos Jurídicos + Administrativos

        </small>

    </div>

    <div class="card-body">

        <div id="calendar"></div>

    </div>

</div>

{{-- ========================================= --}}
{{-- MODAL EVENTO ADMINISTRATIVO --}}
{{-- ========================================= --}}

<div
    class="modal fade"
    id="modalAdminEvent"
    tabindex="-1"
>
    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">

                    Evento Administrativo

                </h5>

                <button
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>

            </div>

            <div class="modal-body">

                <form id="adminEventForm">

                    <input
                        type="hidden"
                        id="admin_event_id"
                    >

                    <div class="mb-2">

                        <label>Tipo</label>

                        <select
                            id="admin_event_type"
                            class="form-select"
                        >

                            <option value="">
                                Seleccionar
                            </option>

                            @foreach(
                                config('options.agenda_event_types')
                                as $key => $label
                            )

                                <option value="{{ $key }}">
                                    {{ $label }}
                                </option>

                            @endforeach

                        </select>

                    </div>

                    <div class="mb-2">

                        <label>Título</label>

                        <input
                            type="text"
                            id="admin_event_title"
                            class="form-control"
                        >

                    </div>

                    <div class="mb-2">

                        <label>Descripción</label>

                        <textarea
                            id="admin_event_description"
                            class="form-control"
                            rows="3"
                        ></textarea>

                    </div>

                    <div class="row">

                        <div class="col-md-6">

                            <label>Fecha Inicio</label>

                            <input
                                type="date"
                                id="admin_event_start_date"
                                class="form-control"
                            >

                        </div>

                        <div class="col-md-6">

                            <label>Hora Inicio</label>
                            <select id="admin_event_start_time" class="form-select form-control-sm"> </select>
                        </div>

                    </div>

                    <div class="row mt-2">

                        <div class="col-md-6">

                            <label>Fecha Fin</label>

                            <input
                                type="date"
                                id="admin_event_end_date"
                                class="form-control"
                            >

                        </div>

                        <div class="col-md-6">

                            <label>Hora Fin</label>
                            <select id="admin_event_end_time" class="form-select form-control-sm"> </select>

                        </div>

                    </div>

                    <div class="mt-2">

                        <label>Lugar</label>

                        <input
                            type="text"
                            id="admin_event_location"
                            class="form-control"
                        >

                    </div>

                </form>

            </div>

            <div class="modal-footer">

                <button
                    class="btn btn-danger"
                    id="btnDeleteAdminEvent"
                    style="display:none"
                >
                    Eliminar
                </button>

                <button
                    class="btn btn-primary"
                    id="btnSaveAdminEvent"
                >
                    Guardar
                </button>

            </div>

        </div>

    </div>

</div>