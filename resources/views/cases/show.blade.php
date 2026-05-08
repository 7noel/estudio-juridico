@extends('layouts.app')

@section('content')

<div class="card">

    {{-- HEADER --}}
    <div class="card-header d-flex justify-content-between align-items-center">

        <h6 class="mb-0">
            Caso #{{ $case->id }}
        </h6>

        <div class="d-flex gap-2">

            {{-- BOTONES SEGÚN ESTADO --}}
            @if($case->status == 'open')
                <button class="btn btn-sm btn-primary btn-change-status" data-status="in_progress">
                    <i class="bi bi-play-fill"></i> Iniciar Caso
                </button>
            @endif

            @if($case->status == 'in_progress')
                <button class="btn btn-sm btn-warning btn-change-status" data-status="on_hold">
                    <i class="bi bi-pause-fill"></i> Pausar Caso
                </button>

                <button class="btn btn-sm btn-success btn-change-status" data-status="closed">
                    <i class="bi bi-check-circle-fill"></i> Cerrar Caso
                </button>
            @endif

            @if($case->status == 'on_hold')
                <button class="btn btn-sm btn-primary btn-change-status" data-status="in_progress">
                    <i class="bi bi-play-circle"></i> Reanudar Caso
                </button>

                <button class="btn btn-sm btn-success btn-change-status" data-status="closed">
                    <i class="bi bi-check-circle-fill"></i> Cerrar Caso
                </button>
            @endif

        </div>

    </div>

    {{-- BODY --}}
    <div class="card-body">

        {{-- INFO PRINCIPAL --}}
        <div class="card mb-3">
            <div class="card-header">Información del caso</div>

            <div class="card-body row">

                <div class="col-md-3">
                    <strong>Cliente:</strong><br>
                    {{ $case->client->full_name ?? '-' }}
                </div>

                <div class="col-md-3">
                    <strong>Abogado:</strong><br>
                    {{ $case->lawyer->name ?? '-' }}
                </div>

                <div class="col-md-3">
                    <strong>Tipo:</strong><br>
                    {{ config('options.service_types')[$case->service_type] ?? '-' }}
                </div>

                <div class="col-md-3">
                    <strong>Estado:</strong><br>
                    <span class="badge bg-{{ config('options.case_status_colors')[$case->status] }}">
                        {{ config('options.case_statuses')[$case->status] }}
                    </span>
                </div>

                <div class="col-md-6 mt-3">
                    <strong>Especialidad:</strong><br>
                    {{ $case->specialty->name ?? '-' }}
                </div>

                <div class="col-md-6 mt-3">
                    <strong>Materia:</strong><br>
                    {{ $case->subject->name ?? '-' }}
                </div>

                <div class="col-md-12 mt-3">
                    <strong>Título:</strong><br>
                    {{ $case->title }}
                </div>

                <div class="col-md-12 mt-3">
                    <strong>Descripción:</strong><br>
                    {{ $case->description ?? 'Sin descripción' }}
                </div>

            </div>
        </div>

@include('cases.partials.activities')
@include('cases.partials.documents')
@include('cases.partials.events')

    </div>

</div>


@endsection


@push('scripts')
<script>
let caseId = {{ $case->id }};
let form = $('#form-activity');

let subtypes = {
    legal: @json(config('options.activity_types')),
    communication: @json(config('options.communication_types')),
    note: {}
};

function loadSubtypes(type, selected = null){
    let options = subtypes[type] || {};
    let html = '';

    for(let k in options){
        let sel = selected == k ? 'selected' : '';
        html += `<option value="${k}" ${sel}>${options[k]}</option>`;
    }

    $('#activity_subtype').html(html);
}

// =============================
// CAMBIO DE TIPO
// =============================
$('#activity_type').change(function(){
    loadSubtypes($(this).val());
});

$('#btnAddActivity').click(function () {

    // 🔥 LIMPIAR MODO EDICIÓN
    $('#activity_id').val('');

    // 🔥 RESET FORM
    $('#form-activity')[0].reset();

    // 🔥 TÍTULO
    $('#modalTitle').text('Agregar actividad');

    // 🔥 FECHA ACTUAL
    let now = getLocalDateTime();
    $('[name="activity_at"]').val(now);

    // 🔥 SUBTIPOS
    loadSubtypes($('#activity_type').val());

});

// =============================
// ABRIR MODAL (CREAR)
// =============================
$('#modalActivity').on('show.bs.modal', function (e) {

    // SI NO ES EDICIÓN → limpiar
    if(!$('#activity_id').val()){

        form.trigger('reset');
        $('#modalTitle').text('Agregar actividad');

        let now = new Date().toISOString().slice(0,16);
        $('[name="activity_at"]').val(now);

        loadSubtypes($('#activity_type').val());
    }

});

// =============================
// EDITAR ACTIVIDAD
// =============================
$(document).on('click', '.btn-edit-activity', function () {

    let btn = $(this);

    $('#activity_id').val(btn.data('id'));

    $('#activity_type').val(btn.data('type'));

    loadSubtypes(btn.data('type'), btn.data('subtype'));

    $('[name="title"]').val(btn.data('title'));
    $('[name="description"]').val(btn.data('description'));
    $('[name="activity_at"]').val(btn.data('date'));

    $('#modalTitle').text('Editar actividad');

    $('#modalActivity').modal('show');
});

// =============================
// GUARDAR (CREATE / UPDATE)
// =============================
form.submit(function(e){
    e.preventDefault();

    let id = $('#activity_id').val();

    let url = id
        ? `/activities/${id}`
        : `{{ route('cases.activities.store', $case->id) }}`;

    let method = id ? 'PUT' : 'POST';

    $.ajax({
        url: url,
        type: method,
        data: form.serialize(),
        success: function(){
            location.reload();
        }
    });
});

// =============================
// ELIMINAR
// =============================
$(document).on('click', '.btn-delete-activity', function(){

    if(!confirm('¿Eliminar actividad?')) return;

    let id = $(this).data('id');

    $.ajax({
        url: `/activities/${id}`,
        type: 'DELETE',
        data: {_token: '{{ csrf_token() }}'},
        success: function(){
            location.reload();
        }
    });

});

// =============================
// CAMBIAR ESTADO
// =============================
$(document).on('click', '.btn-change-status', function(){

    let status = $(this).data('status');

    if(confirm('¿Cambiar estado del caso?')){

        $.post("{{ route('cases.change-status', $case->id) }}", {
            _token: '{{ csrf_token() }}',
            status: status
        }, function(){
            location.reload();
        });

    }

});

// let caseId = {{ $case->id }};
let mode = 'create'; // create | edit
let currentDocumentId = null;

// =================
// CREAR / EDITAR
// =================
$('#form-document').submit(function(e){
    e.preventDefault();

    let id = $('#doc_id').val();

    let formData = new FormData(this);

    let url = id
        ? `/documents/${id}`
        : `/cases/${caseId}/documents`;

    let method = id ? 'POST' : 'POST';

    if(id){
        formData.append('_method', 'PUT');
    }

    $.ajax({
        url: url,
        method: method,
        data: formData,
        processData: false,
        contentType: false,
        success: function(){
            location.reload();
        }
    });
});

// =================
// EDITAR
// =================
$(document).on('click', '.btn-edit-doc', function(){
    mode = 'edit';

    const id = $(this).data('id');
    currentDocumentId = id;

    const title = $(this).data('title');
    const type = $(this).data('type');
    const file = $(this).data('file'); // IMPORTANTE
    const file_name = $(this).data('file_name'); // IMPORTANTE

    $('#doc_id').val(currentDocumentId);
    $('[name="title"]').val(title);
    $('[name="document_type"]').val(type);

    // 🔥 OCULTAR INPUT FILE
    $('#fileInputWrapper').hide();

    // 🔥 MOSTRAR LINK ACTUAL
    $('#currentFileWrapper').removeClass('d-none');
    $('#currentFileLink').attr('href', '/storage/' + file);
    $('#currentFileLink').text('Ver: ' + file_name);


    $('#docModalTitle').text('Editar documento');

    $('#modalDocument').modal('show');
});

// =================
// NUEVO
// =================
$('#btnAddDocument').click(function(){
    mode = 'create';
    currentDocumentId = null;

    $('#doc_id').val('');
    $('#form-document')[0].reset();
    $('#docModalTitle').text('Subir documento');

    // 🔥 MOSTRAR INPUT FILE
    $('#fileInputWrapper').show();

    // 🔥 OCULTAR LINK
    $('#currentFileWrapper').addClass('d-none');
});

// =================
// ELIMINAR
// =================
$(document).on('click', '.btn-delete-doc', function(){

    if(!confirm('¿Eliminar documento?')) return;

    let id = $(this).data('id');

    $.ajax({
        url: `/documents/${id}`,
        type: 'DELETE',
        data: {_token: '{{ csrf_token() }}'},
        success: function(){
            location.reload();
        }
    });
});

let eventMode = 'create';
let eventId = null;
// const caseId = {{ $case->id }};

// abrir crear
$('#btnAddEvent').click(function () {
    eventMode = 'create';
    eventId = null;

    $('#eventForm')[0].reset();

    let now = new Date();
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    $('#event_start').val(now.toISOString().slice(0,16));

    $('#modalEvent').modal('show');
});

// editar
$(document).on('click', '.btn-edit-event', function () {
    eventMode = 'edit';

    eventId = $(this).data('id');

    $('#event_title').val($(this).data('title'));
    $('#event_description').val($(this).data('description'));
    $('#event_start').val($(this).data('start'));
    $('#event_end').val($(this).data('end'));
    $('#event_location').val($(this).data('location'));

    $('#modalEvent').modal('show');
});

// guardar
$('#saveEvent').click(function () {

    let url = eventMode === 'create'
        ? `/cases/${caseId}/agenda`
        : `/agenda/${eventId}`;

    let method = eventMode === 'create' ? 'POST' : 'PUT';

    $.ajax({
        url: url,
        method: method,
        data: {
            _token: '{{ csrf_token() }}',
            title: $('#event_title').val(),
            description: $('#event_description').val(),
            start_datetime: $('#event_start').val(),
            end_datetime: $('#event_end').val(),
            location: $('#event_location').val(),
        },
        success: () => location.reload()
    });
});

// eliminar
$(document).on('click', '.btn-delete-event', function () {
    let id = $(this).data('id');

    if (!confirm('¿Eliminar evento?')) return;

    $.ajax({
        url: `/agenda/${id}`,
        method: 'DELETE',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: () => location.reload()
    });
});


document.addEventListener('DOMContentLoaded', function () {

    const calendarEl = document.getElementById('calendar');

    const calendar = new FullCalendar.Calendar(calendarEl, {

        initialView: 'dayGridMonth',

        locale: 'es',

        height: 600,

        events: `/cases/{{ $case->id }}/agenda/events`,

        selectable: true,

        editable: true,

        slotDuration: '00:15:00',
        snapDuration: '00:15:00',

        // 🔥 CLICK EN DÍA (crear evento)
        dateClick: function(info) {

            eventMode = 'create';
            eventId = null;

            $('#eventForm')[0].reset();

            $('#event_start').val(info.dateStr + "T08:00");

            $('#modalEvent').modal('show');
        },

        // 🔥 CLICK EN EVENTO (editar)
        eventClick: function(info) {

            const event = info.event;

            eventMode = 'edit';
            eventId = event.id;

            // 🔥 FUNCIÓN PARA FORMATEAR
            function formatDate(date) {
                if (!date) return '';
                
                const d = new Date(date);
                
                const year = d.getFullYear();
                const month = String(d.getMonth() + 1).padStart(2, '0');
                const day = String(d.getDate()).padStart(2, '0');
                const hours = String(d.getHours()).padStart(2, '0');
                const minutes = String(d.getMinutes()).padStart(2, '0');

                return `${year}-${month}-${day}T${hours}:${minutes}`;
            }

            $('#event_title').val(event.title);
            $('#event_description').val(event.extendedProps.description ?? '');
            $('#event_location').val(event.extendedProps.location ?? '');

            // 🔥 AQUÍ ESTÁ LA SOLUCIÓN
            $('#event_start').val(formatDate(event.start));
            $('#event_end').val(formatDate(event.end));

            $('#modalEvent').modal('show');
        },

        eventDrop: function(info) {
            $.ajax({
                url: `/agenda/${info.event.id}`,
                method: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}',
                    start_datetime: info.event.startStr,
                    end_datetime: info.event.endStr
                },
                error: function() {
                    alert('Error al actualizar evento');
                    info.revert(); // 🔥 vuelve al estado anterior
                }
            });
        },

        eventResize: function(info) {

            $.ajax({
                url: `/agenda/${info.event.id}`,
                method: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}',
                    start_datetime: info.event.startStr,
                    end_datetime: info.event.endStr
                },
                error: function() {
                    alert('Error al actualizar evento');
                    info.revert(); // 🔥 vuelve al estado anterior
                }
            });

        },

        eventDidMount: function(info) {
            if (info.event.extendedProps.description) {
                $(info.el).tooltip({
                    title: info.event.extendedProps.description,
                    placement: 'top',
                    trigger: 'hover',
                    container: 'body'
                });
            }
        }

    });

    calendar.render();

});




</script>
@endpush