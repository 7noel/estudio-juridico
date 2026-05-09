<div class="card mt-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <strong>Actividades</strong>
        @if($canManageCaseContent)
        <button class="btn btn-sm btn-outline-primary" id="btnAddActivity"
                data-bs-toggle="modal"
                data-bs-target="#modalActivity">
            <i class="bi bi-plus"></i> Agregar
        </button>
        @endif
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
                    @if($canManageCaseContent)
                    <div class="text-end mt-2">
                        <div class="text-end mt-2 d-flex justify-content-end gap-2">

                            <button class="btn btn-sm btn-outline-success btn-edit-activity"
                                        data-id="{{ $act->id }}"
                                        data-type="{{ $act->type }}"
                                        data-subtype="{{ $act->subtype }}"
                                        data-title="{{ $act->title }}"
                                        data-description="{{ $act->description }}"
                                        data-date="{{ $act->activity_at?->format('Y-m-d\TH:i') }}"
                                        data-has-event="{{ $act->agendaEvent ? 1 : 0 }}"
                                        {{-- EVENTO --}}
                                        data-agenda-title="{{ $act->agendaEvent?->title }}"
                                        data-agenda-description="{{ $act->agendaEvent?->description }}"
                                        data-agenda-start="{{ $act->agendaEvent?->start_datetime?->format('Y-m-d H:i:s') }}"
                                        data-agenda-end="{{ $act->agendaEvent?->end_datetime?->format('Y-m-d H:i:s') }}"
                                        data-agenda-location="{{ $act->agendaEvent?->location }}">
                                <i class="bi bi-pencil"></i> Editar
                            </button>

                            <button class="btn btn-sm btn-outline-danger btn-delete-activity"
                                    data-id="{{ $act->id }}">
                                <i class="bi bi-trash"></i> Eliminar
                            </button>

                        </div>

                    </div>
                    @endif

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
                        <select name="type" id="activity_type" class="form-control form-control-sm">
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

                        <input class="form-check-input"
                               type="checkbox"
                               id="create_agenda_event"
                               value="1">

                        <label class="form-check-label">
                            Crear evento en agenda
                        </label>

                    </div>

                    <div id="activityAgendaFields" style="display:none;">

                        <div class="row">

                            <div class="alert alert-info py-2 mb-3 d-none"
                                 id="activityAgendaLinkedMessage">

                                Esta actividad tiene un evento agenda asociado.

                            </div>

                            <div class="mb-2">
                                <label>Título evento</label>

                                <input type="text"
                                       id="activity_event_title"
                                       class="form-control form-control-sm">
                            </div>

                            <div class="mb-2">
                                <label>Descripción evento</label>

                                <textarea id="activity_event_description"
                                          class="form-control form-control-sm"
                                          rows="2"></textarea>
                            </div>

                            <div class="col-md-6 mb-2">
                                <label>Fecha inicio</label>

                                <input type="date"
                                       id="activity_event_start_date"
                                       class="form-control form-control-sm">
                            </div>

                            <div class="col-md-6 mb-2">
                                <label>Hora inicio</label>

                                <select id="activity_event_start_time"
                                        class="form-select form-control-sm">
                                </select>
                            </div>

                        </div>

                        <div class="row">

                            <div class="col-md-6 mb-2">
                                <label>Fecha fin</label>

                                <input type="date"
                                       id="activity_event_end_date"
                                       class="form-control form-control-sm">
                            </div>

                            <div class="col-md-6 mb-2">
                                <label>Hora fin</label>

                                <select id="activity_event_end_time"
                                        class="form-select form-control-sm">
                                </select>
                            </div>

                        </div>

                        <div class="mb-2">

                            <label>Lugar</label>

                            <input type="text"
                                   id="activity_event_location"
                                   class="form-control form-control-sm">

                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">Guardar</button>
                </div>

            </form>

        </div>
    </div>
</div>

@push('scripts')
<script>


let activityForm = $('#form-activity');

let activitySubtypes = {

    legal: @json(config('options.activity_types')),

    communication: @json(config('options.communication_types')),

    note: {}

};

// =====================================================
// SUBTIPOS
// =====================================================

function loadActivitySubtypes(type, selected = null){

    let options = activitySubtypes[type] || {};

    let html = '';

    for(let k in options){

        let sel = selected == k ? 'selected' : '';

        html += `
            <option value="${k}" ${sel}>
                ${options[k]}
            </option>
        `;
    }

    $('#activity_subtype').html(html);
}

// =====================================================
// CAMBIO TIPO
// =====================================================

$('#activity_type').change(function(){

    loadActivitySubtypes($(this).val());

});

// =====================================================
// AUTOCOMPLETAR TITULO
// =====================================================

$('#activity_subtype').change(function(){

    let text = $(this).find('option:selected').text();

    if(!$('[name="title"]').val()){

        $('[name="title"]').val(text);

    }

    // 🔥 titulo evento
    if(!$('#activity_event_title').val()){

        $('#activity_event_title').val(text);

    }

});

// =====================================================
// ABRIR NUEVA ACTIVIDAD
// =====================================================

$('#btnAddActivity').click(function(){


    $('#activity_id').val('');

    activityForm[0].reset();

    $('#modalTitle').text('Agregar actividad');

    loadActivitySubtypes(
        $('#activity_type').val()
    );

    $('[name="activity_at"]').val(
        getLocalDateTime()
    );

    // =========================================
    // AGENDA
    // =========================================

    $('#create_agenda_event_wrapper').show();

    $('#create_agenda_event').prop('checked', false);

    $('#activityAgendaFields').hide();

    $('#activityAgendaLinkedMessage').addClass('d-none');

    // 🔥 HORA REDONDEADA
    let now = roundToNext15Minutes(new Date());

    // 🔥 FECHAS
    $('#activity_event_start_date').val(
        formatDate(now)
    );

    $('#activity_event_end_date').val(
        formatDate(now)
    );

    // 🔥 HORAS
    let startTime = formatTime(now);

    $('#activity_event_start_time').val(
        startTime
    );

    $('#activity_event_end_time').val(
        add60Minutes(startTime)
    );

    // 🔥 LIMPIAR CAMPOS
    $('#activity_event_title').val('');

    $('#activity_event_description').val('');

    $('#activity_event_location').val('');

});

// =====================================================
// MOSTRAR / OCULTAR AGENDA
// =====================================================

$(document).on(
    'change',
    '#create_agenda_event',
    function(){

        if($(this).is(':checked')){

            $('#activityAgendaFields').show();

            // =========================================
            // FECHA/HORA ACTUAL REDONDEADA
            // =========================================

            let now = roundToNext15Minutes(
                new Date()
            );

            // =========================================
            // FECHAS
            // =========================================

            $('#activity_event_start_date').val(
                formatDate(now)
            );

            $('#activity_event_end_date').val(
                formatDate(now)
            );

            // =========================================
            // HORAS
            // =========================================

            let startTime = formatTime(now);

            $('#activity_event_start_time').val(
                startTime
            );

            $('#activity_event_end_time').val(
                add60Minutes(startTime)
            );

            // autocompletar titulo
            if(!$('#activity_event_title').val()){

                $('#activity_event_title').val(
                    $('[name="title"]').val()
                );

            }

            // autocompletar descripcion
            if(!$('#activity_event_description').val()){

                $('#activity_event_description').val(
                    $('[name="description"]').val()
                );

            }

        } else {

            $('#activityAgendaFields').hide();

        }

    }
);

// =====================================================
// CAMBIO HORA INICIO
// =====================================================

$(document).on(
    'change',
    '#activity_event_start_time',
    function(){

        let startTime = $(this).val();

        $('#activity_event_end_time').val(
            add60Minutes(startTime)
        );

    }
);

// =====================================================
// EDITAR ACTIVIDAD
// =====================================================

$(document).on(
    'click',
    '.btn-edit-activity',
    function(){


        let btn = $(this);

        let hasEvent = btn.data('has-event');

        // =====================================
        // ACTIVIDAD
        // =====================================

        $('#activity_id').val(
            btn.data('id')
        );

        $('#activity_type').val(
            btn.data('type')
        );

        loadActivitySubtypes(
            btn.data('type'),
            btn.data('subtype')
        );

        $('[name="title"]').val(
            btn.data('title')
        );

        $('[name="description"]').val(
            btn.data('description')
        );

        $('[name="activity_at"]').val(
            btn.data('date')
        );

        $('#modalTitle').text(
            'Editar actividad'
        );

        // =====================================
        // AGENDA
        // =====================================

        $('#activityAgendaFields').hide();

        $('#activityAgendaLinkedMessage')
            .addClass('d-none');

        if(hasEvent){

            $('#create_agenda_event_wrapper')
                .hide();

            $('#activityAgendaFields')
                .show();

            $('#activityAgendaLinkedMessage')
                .removeClass('d-none');

            // =====================================
            // DATOS EVENTO
            // =====================================

            let agendaTitle =
                btn.data('agenda-title');

            let agendaDescription =
                btn.data('agenda-description');

            let agendaStart =
                btn.data('agenda-start');

            let agendaEnd =
                btn.data('agenda-end');

            let agendaLocation =
                btn.data('agenda-location');

            // =====================================
            // TITULO / DESCRIPCION
            // =====================================

            $('#activity_event_title').val(
                agendaTitle ?? ''
            );

            $('#activity_event_description').val(
                agendaDescription ?? ''
            );

            $('#activity_event_location').val(
                agendaLocation ?? ''
            );

            // =====================================
            // FECHA INICIO
            // =====================================

            if(agendaStart){

                let start = new Date(
                    agendaStart.replace(' ', 'T')
                );

                $('#activity_event_start_date').val(
                    formatDate(start)
                );

                $('#activity_event_start_time').val(
                    formatTime(start)
                );
            }

            // =====================================
            // FECHA FIN
            // =====================================

            if(agendaEnd){

                let end = new Date(
                    agendaEnd.replace(' ', 'T')
                );

                $('#activity_event_end_date').val(
                    formatDate(end)
                );

                $('#activity_event_end_time').val(
                    formatTime(end)
                );

            }

        } else {

            $('#create_agenda_event_wrapper')
                .show();

            $('#activityAgendaFields')
                .hide();

        }

        $('#modalActivity').modal('show');

    }
);

// =====================================================
// GUARDAR
// =====================================================

activityForm.submit(function(e){

    e.preventDefault();

    let id = $('#activity_id').val();

    let url = id
        ? `/activities/${id}`
        : `/cases/${caseId}/activities`;

    let method = id
        ? 'PUT'
        : 'POST';

    // =====================================
    // VALIDAR FECHAS
    // =====================================

    let start_datetime =
        $('#activity_event_start_date').val()
        + ' ' +
        $('#activity_event_start_time').val();

    let end_datetime =
        $('#activity_event_end_date').val()
        + ' ' +
        $('#activity_event_end_time').val();

    if(
        (
            $('#create_agenda_event').is(':checked')
            ||
            $('.btn-edit-activity').data('has-event')
        )
        &&
        end_datetime <= start_datetime
    ){

        alert(
            'La fecha fin debe ser mayor'
        );

        return;
    }

    $.ajax({

        url: url,

        type: method,

        data: {

            _token: '{{ csrf_token() }}',

            title:
                $('[name="title"]').val(),

            type:
                $('#activity_type').val(),

            subtype:
                $('#activity_subtype').val(),

            description:
                $('[name="description"]').val(),

            activity_at:
                $('[name="activity_at"]').val(),

            // =================================
            // AGENDA
            // =================================

            create_agenda_event:
                $('#create_agenda_event')
                    .is(':checked') ? 1 : 0,

            agenda_title:
                $('#activity_event_title').val(),

            agenda_description:
                $('#activity_event_description').val(),

            agenda_start_datetime:
                $('#activity_event_start_date').val()
                + ' ' +
                $('#activity_event_start_time').val()
                + ':00',

            agenda_end_datetime:
                $('#activity_event_end_date').val()
                + ' ' +
                $('#activity_event_end_time').val()
                + ':00',

            agenda_location:
                $('#activity_event_location').val(),

        },

        success: function(){

            location.reload();

        },

        error: function(xhr){

            console.log(xhr.responseText);

            alert('Error al guardar');

        }

    });

});

// =====================================================
// ELIMINAR
// =====================================================

$(document).on(
    'click',
    '.btn-delete-activity',
    function(){

        if(!confirm(
            '¿Eliminar actividad?'
        )) return;

        let id = $(this).data('id');

        $.ajax({

            url: `/activities/${id}`,

            type: 'DELETE',

            data: {

                _token: '{{ csrf_token() }}'

            },

            success: function(){

                location.reload();

            }

        });

    }
);

</script>
@endpush