<div
    class="modal fade"
    id="followUpModal"
    tabindex="-1"
    aria-labelledby="followUpModalLabel"
    aria-hidden="true"
>

    <div class="modal-dialog modal-lg">

        <div class="modal-content">

            <form
                action="{{ route('follow-ups.store') }}"
                method="POST"
            >

                @csrf

                <input
                    type="hidden"
                    name="consultation_id"
                    value="{{ $consultation->id }}"
                >

                <div class="modal-header">

                    <h5
                        class="modal-title"
                        id="followUpModalLabel"
                    >
                        Nuevo seguimiento
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                    ></button>

                </div>

                <div class="modal-body">

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <x-form.input
                                name="contact_date"
                                label="Fecha del contacto"
                                type="date"
                                :value="now()->format('Y-m-d')"
                                required
                            />

                        </div>

                        <div class="col-md-6 mb-3">

                            <x-form.input
                                name="next_contact_date"
                                label="Próximo contacto"
                                type="date"
                            />

                            <small class="text-muted">
                                Opcional.
                            </small>

                        </div>

                        <div class="col-md-6 mb-3">

                            <x-form.select
                                name="communication_type"
                                label="Tipo de comunicación"
                                :options="config('options.communication_types')"
                                placeholder="Seleccione..."
                                required
                            />

                        </div>

                        <div class="col-md-6 mb-3">

                            <x-form.select
                                name="result"
                                label="Resultado"
                                :options="config('options.follow_up_results')"
                                placeholder="Seleccione..."
                                required
                            />

                        </div>

                        <div class="col-12">

                            <x-form.textarea
                                name="notes"
                                label="Observaciones"
                                rows="5"
                            />

                        </div>
                    </div>

                    <div id="followUpActions" class="mt-3 d-none">
                        <div id="generateCaseContainer" class="form-check d-none">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                id="generate_case"
                                name="generate_case"
                                value="1"
                                checked>

                            <label class="form-check-label text-success" for="generate_case">
                                Generar caso al guardar
                            </label>

                            <div class="form-text text-success">
                                El cliente ha aceptado contratar los servicios.
                            </div>
                        </div>

                        <div id="rejectConsultationContainer" class="form-check d-none">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                id="reject_consultation"
                                name="reject_consultation"
                                value="1"
                                checked>

                            <label class="form-check-label text-danger" for="reject_consultation">
                                Rechazar consulta al guardar
                            </label>

                            <div class="form-text text-danger">
                                La consulta se marcará como rechazada.
                            </div>
                        </div>
                    </div>
                </div>


                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-outline-secondary"
                        data-bs-dismiss="modal"
                    >

                        Cancelar

                    </button>

                    <x-form.button class="px-4">

                        <i class="bi bi-save"></i>

                        Guardar seguimiento

                    </x-form.button>

                </div>

            </form>

        </div>

    </div>

</div>