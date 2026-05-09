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

    $('#create_agenda_event').prop('checked', false);



    $('#activityAgendaFields').hide();

    let now = roundToNext15Minutes(new Date());

    $('#activity_event_start_date').val(
        formatDate(now)
    );

    $('#activity_event_end_date').val(
        formatDate(now)
    );

    $('#activity_event_start_time').val(
        formatTime(now)
    );

    $('#activity_event_end_time').val(
        add60Minutes(formatTime(now))
    );

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

// =====================================================
// AGENDA
// =====================================================

let eventMode = 'create';
let eventId = null;

// =====================================================
// ELIMINA ERROR EN CONSOLA POR EL FOCUS
// =====================================================

$('#modalEvent').on('hidden.bs.modal', function () {

    document.activeElement.blur();

});
$('#modalDocument').on('hidden.bs.modal', function () {

    document.activeElement.blur();

});

// =====================================================
// GENERAR HORAS CADA 15 MIN
// =====================================================

function generateTimeOptions(selector) {

    let html = '';

    // desde 7am hasta 10pm
    for(let h = 7; h <= 22; h++) {

        for(let m = 0; m < 60; m += 15) {

            // 🔥 VALOR REAL QUE SE GUARDA
            let hour24 = String(h).padStart(2, '0');
            let minute = String(m).padStart(2, '0');

            let value = `${hour24}:${minute}`;

            // 🔥 TEXTO VISUAL AM/PM
            let period = h >= 12 ? 'p. m.' : 'a. m.';

            let hour12 = h % 12;

            if(hour12 === 0){
                hour12 = 12;
            }

            let label = `${hour12}:${minute} ${period}`;

            html += `
                <option value="${value}">
                    ${label}
                </option>
            `;
        }
    }

    $(selector).html(html);
}

generateTimeOptions('#event_start_time');
generateTimeOptions('#event_end_time');

// =====================================================
// FORMATEAR FECHA
// =====================================================

function formatDate(date){

    if(!date) return '';

    const d = new Date(date);

    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');

    return `${year}-${month}-${day}`;
}

// =====================================================
// FORMATEAR HORA
// =====================================================

function formatTime(date){

    if(!date) return '';

    const d = new Date(date);

    const hour = String(d.getHours()).padStart(2, '0');
    const min = String(d.getMinutes()).padStart(2, '0');

    return `${hour}:${min}`;
}

// =====================================================
// REDONDEA HORA ACTUAL
// =====================================================

function roundToNext15Minutes(date){

    let d = new Date(date);

    d.setSeconds(0);
    d.setMilliseconds(0);

    let minutes = d.getMinutes();

    let rounded = Math.ceil(minutes / 15) * 15;

    // 🔥 si llega a 60
    if(rounded === 60){

        d.setHours(d.getHours() + 1);
        d.setMinutes(0);

    } else {

        d.setMinutes(rounded);

    }

    return d;
}

// =====================================================
// SUMAR 15 MINUTOS
// =====================================================

function add15Minutes(time){

    let [hour, min] = time.split(':');

    let date = new Date();

    date.setHours(hour);
    date.setMinutes(min);

    date.setMinutes(date.getMinutes() + 15);

    let h = String(date.getHours()).padStart(2, '0');
    let m = String(date.getMinutes()).padStart(2, '0');

    return `${h}:${m}`;
}

// =====================================================
// SUMAR 60 MINUTOS
// =====================================================

function add60Minutes(time){

    let [hour, min] = time.split(':');

    let date = new Date();

    date.setHours(hour);
    date.setMinutes(min);

    date.setMinutes(date.getMinutes() + 60);

    let h = String(date.getHours()).padStart(2, '0');
    let m = String(date.getMinutes()).padStart(2, '0');

    return `${h}:${m}`;
}

// =====================================================
// CUANDO CAMBIA HORA INICIO
// =====================================================

$('#event_start_time').change(function(){

    let startTime = $(this).val();

    let endTime = add60Minutes(startTime);

    $('#event_end_time').val(endTime);

});

// =====================================================
// ABRIR NUEVO EVENTO
// =====================================================

$('#btnAddEvent').click(function(){

    eventMode = 'create';
    eventId = null;

    $('#eventForm')[0].reset();

    let now = roundToNext15Minutes(new Date());
    let date = formatDate(now);
    let time = formatTime(now);

    $('#event_start_date').val(date);
    $('#event_end_date').val(date);

    $('#event_start_time').val(time);

    $('#event_end_time').val(add60Minutes(time));

    $('#modalEvent').modal('show');

});

// =====================================================
// EDITAR EVENTO
// =====================================================

$(document).on('click', '.btn-edit-event', function(){

    eventMode = 'edit';

    eventId = $(this).data('id');

    $('#event_title').val($(this).data('title'));

    $('#event_description').val($(this).data('description'));

    $('#event_location').val($(this).data('location'));

    let start = $(this).data('start');
    let end = $(this).data('end');

    if(start){

        let s = new Date(start);

        $('#event_start_date').val(formatDate(s));
        $('#event_start_time').val(formatTime(s));
    }

    if(end){

        let e = new Date(end);

        $('#event_end_date').val(formatDate(e));
        $('#event_end_time').val(formatTime(e));
    }

    $('#modalEvent').modal('show');

});

// =====================================================
// GUARDAR EVENTO
// =====================================================

$('#saveEvent').click(function(){

    let start_datetime =
        $('#event_start_date').val()
        + ' ' +
        $('#event_start_time').val()
        + ':00';

    let end_datetime =
        $('#event_end_date').val()
        + ' ' +
        $('#event_end_time').val()
        + ':00';

    let url = eventMode === 'create'
        ? `/cases/${caseId}/agenda`
        : `/agenda/${eventId}`;

    let method = eventMode === 'create'
        ? 'POST'
        : 'PUT';

    $.ajax({

        url: url,

        method: method,

        data: {

            _token: '{{ csrf_token() }}',

            title: $('#event_title').val(),

            description: $('#event_description').val(),

            start_datetime: start_datetime,

            end_datetime: end_datetime,

            location: $('#event_location').val(),

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
// ELIMINAR EVENTO
// =====================================================

$(document).on('click', '.btn-delete-event', function(){

    if(!confirm('¿Eliminar evento?')) return;

    let id = $(this).data('id');

    $.ajax({

        url: `/agenda/${id}`,

        type: 'DELETE',

        data: {

            _token: '{{ csrf_token() }}'

        },

        success: function(){

            location.reload();

        }

    });

});

// =====================================================
// FULL CALENDAR
// =====================================================

document.addEventListener('DOMContentLoaded', function () {

    const calendarEl = document.getElementById('calendar');

    const calendar = new FullCalendar.Calendar(calendarEl, {

        initialView: 'dayGridMonth',

        locale: 'es',

        height: 650,

        selectable: true,

        editable: true,

        slotDuration: '00:15:00',

        snapDuration: '00:15:00',

        events: `/cases/{{ $case->id }}/agenda/events`,

        // ==========================================
        // CLICK EN DÍA
        // ==========================================

        dateClick: function(info){

            eventMode = 'create';
            eventId = null;

            $('#eventForm')[0].reset();

            $('#event_start_date').val(info.dateStr);
            $('#event_end_date').val(info.dateStr);

            $('#event_start_time').val('08:00');
            $('#event_end_time').val('08:15');

            $('#modalEvent').modal('show');

        },

        // ==========================================
        // CLICK EVENTO
        // ==========================================

        eventClick: function(info){

            const event = info.event;

            eventMode = 'edit';

            eventId = event.id;

            $('#event_title').val(event.title);

            $('#event_description').val(
                event.extendedProps.description ?? ''
            );

            $('#event_location').val(
                event.extendedProps.location ?? ''
            );

            if(event.start){

                $('#event_start_date').val(
                    formatDate(event.start)
                );

                $('#event_start_time').val(
                    formatTime(event.start)
                );

            }

            if(event.end){

                $('#event_end_date').val(
                    formatDate(event.end)
                );

                $('#event_end_time').val(
                    formatTime(event.end)
                );

            }

            $('#modalEvent').modal('show');

        },

        // ==========================================
        // MOVER EVENTO
        // ==========================================

        eventDrop: function(info){

            $.ajax({

                url: `/agenda/${info.event.id}`,

                method: 'PUT',

                data: {

                    _token: '{{ csrf_token() }}',

                    start_datetime: info.event.startStr,

                    end_datetime: info.event.endStr,

                    title: info.event.title

                },

                error: function(){

                    alert('Error al mover evento');

                    info.revert();

                }

            });

        },

        // ==========================================
        // REDIMENSIONAR EVENTO
        // ==========================================

        eventResize: function(info){

            $.ajax({

                url: `/agenda/${info.event.id}`,

                method: 'PUT',

                data: {

                    _token: '{{ csrf_token() }}',

                    start_datetime: info.event.startStr,

                    end_datetime: info.event.endStr,

                    title: info.event.title

                },

                error: function(){

                    alert('Error al cambiar duración');

                    info.revert();

                }

            });

        },

        // ==========================================
        // TOOLTIPS
        // ==========================================

        eventDidMount: function(info){

            if(info.event.extendedProps.description){

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

// ==========================================
// EVENTO EN ACTIVIDAD
// ==========================================

$('#modalActivity').on('shown.bs.modal', function(){

    // 🔥 llenar horas
    generateTimeOptions('#activity_event_start_time');

    generateTimeOptions('#activity_event_end_time');

});

// 🔥 mostrar / ocultar agenda
$(document).on('change', '#create_agenda_event', function(){

    $('#activityAgendaFields').toggle(
        $(this).is(':checked')
    );

});

// 🔥 cambiar hora fin automática
$(document).on('change', '#activity_event_start_time', function(){

    let startTime = $(this).val();

    $('#activity_event_end_time').val(
        add60Minutes(startTime)
    );

});


</script>
@endpush