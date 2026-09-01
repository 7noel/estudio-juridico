<div
    class="modal fade"
    id="modalActivityDetail"
    tabindex="-1"
    aria-hidden="true"
>

    <div class="modal-dialog modal-lg">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">

                    <i class="bi bi-journal-text"></i>

                    Detalle de actividad

                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Cerrar"
                ></button>

            </div>

            <div class="modal-body">

                <div class="row g-3">

                    <div class="col-md-6">

                        <label class="form-label text-muted small mb-0">

                            Fecha y hora

                        </label>

                        <div class="fw-semibold" id="mdActivityAt">

                            -

                        </div>

                    </div>

                    <div class="col-md-6">

                        <label class="form-label text-muted small mb-0">

                            Tipo

                        </label>

                        <div class="fw-semibold" id="mdType">

                            -

                        </div>

                    </div>

                    <div class="col-md-6">

                        <label class="form-label text-muted small mb-0">

                            Subtipo

                        </label>

                        <div class="fw-semibold" id="mdSubtype">

                            -

                        </div>

                    </div>

                    <div class="col-md-6">

                        <label class="form-label text-muted small mb-0">

                            Registrado por

                        </label>

                        <div class="fw-semibold" id="mdUserName">

                            -

                        </div>

                    </div>

                    <div class="col-12">

                        <label class="form-label text-muted small mb-0">

                            Título de la actividad

                        </label>

                        <div class="fw-semibold" id="mdTitle">

                            -

                        </div>

                    </div>

                    <div class="col-12">

                        <label class="form-label text-muted small mb-0">

                            Descripción

                        </label>

                        <div
                            class="border rounded p-2 bg-light"
                            id="mdDescription"
                        >

                            -

                        </div>

                    </div>

                    <div class="col-12">

                        <hr>

                    </div>

                    <div class="col-12">

                        <label class="form-label text-muted small mb-0">

                            Caso

                        </label>

                        <div id="mdCaseInfo">

                            -

                        </div>

                    </div>

                    <div class="col-12" id="mdAgendaBlock" style="display:none;">

                        <hr>

                        <label class="form-label text-muted small mb-0">

                            Evento de agenda asociado

                        </label>

                        <div
                            class="border rounded p-2 bg-light"
                            id="mdAgendaInfo"
                        >

                            -

                        </div>

                    </div>

                </div>

            </div>

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal"
                >

                    Cerrar

                </button>

                <a
                    href="#"
                    target="_blank"
                    class="btn btn-primary"
                    id="mdOpenCase"
                >

                    <i class="bi bi-folder2-open"></i>

                    Abrir caso

                </a>

            </div>

        </div>

    </div>

</div>
