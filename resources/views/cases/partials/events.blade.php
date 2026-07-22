@if(1==0)
<div class="card mt-3">
    <div class="card-header d-flex justify-content-between">
        <strong>Agenda</strong>
        @if($canManageCaseContent)
        <button class="btn btn-sm btn-outline-primary" id="btnAddEvent">
            <i class="bi bi-plus"></i>  Agregar
        </button>
        @endif
    </div>

    <div class="card-body">

        <div id="agenda-list">

            @forelse($case->agendaEvents as $event)
                <div class="border rounded p-2 mb-2">

                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-primary">
                                    {{ config('options.agenda_event_types')[$event->type] ?? $event->type }}
                                </span>
                                <strong>{{ $event->title }}</strong>
                            </div>
                            <small>
                                {{ $event->start_datetime->format('d/m/Y H:i') }}
                            </small>
                        </div>
                    </div>

                    <div class="text-muted">
                        {{ $event->location }}
                    </div>

                    <div class="mt-1">
                        {{ $event->description }}
                    </div>

                    @if($canManageCaseContent)
                    <div class="text-end mt-2">
                        <button class="btn btn-sm btn-outline-primary btn-edit-event"
                            data-id="{{ $event->id }}"
                            data-type="{{ $event->type }}"
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
                    @endif

                </div>
            @empty
                <div class="text-muted">No hay eventos</div>
            @endforelse

        </div>

    </div>
</div>
@endif

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
                        <label>Tipo</label>
                        <select id="event_type" class="form-select form-control-sm">
                            <option value=""> Seleccionar </option>
                            @foreach(config('options.agenda_event_types') as $key => $label)
                                <option value="{{ $key }}"> {{ $label }} </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <label>Título</label>
                        <input type="text" id="event_title" class="form-control form-control-sm text-uppercase">
                    </div>
                    <div class="mb-2">
                        <label>Descripción</label>
                        <textarea id="event_description" class="form-control form-control-sm"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label>Fecha inicio</label>
                            <input type="date" id="event_start_date" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label>Hora inicio</label>
                            <select id="event_start_time" class="form-select form-control-sm"> </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label>Fecha fin</label>
                            <input type="date" id="event_end_date" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label>Hora fin</label>
                            <select id="event_end_time" class="form-select form-control-sm"> </select>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label>Lugar</label>
                        <input type="text" id="event_location" class="form-control form-control-sm">
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button class="btn btn-sm btn-outline-primary" id="saveEvent"> <i class="bi bi-save"></i> Guardar</button>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
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
        generateTimeOptions('#activity_event_start_time');
        generateTimeOptions('#activity_event_end_time');



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

    $('#event_type').val($(this).data('type'));

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

            type: $('#event_type').val(),

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


// ==========================================
// EVENTO EN ACTIVIDAD
// ==========================================

// $('#modalActivity').on('shown.bs.modal', function(){

//     // 🔥 llenar horas
//     generateTimeOptions('#activity_event_start_time');

//     generateTimeOptions('#activity_event_end_time');

// });

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

// =====================================================
// FULL CALENDAR
// =====================================================

let calendar;

document.addEventListener('DOMContentLoaded', function () {

    const calendarEl = document.getElementById('calendar');

    calendar = new FullCalendar.Calendar(calendarEl, {

        initialView: 'dayGridMonth',

        locale: 'es',

        height: 650,

        // selectable: true,
        selectable: canManageCaseContent,

        // editable: true,
        editable: canManageCaseContent,


        slotDuration: '00:15:00',

        snapDuration: '00:15:00',

        events: `/cases/{{ $case->id }}/agenda/events`,

        buttonText: {

            today: 'Hoy',
            month: 'Mes',
            week: 'Semana',
            day: 'día',

        },
        headerToolbar: {

            left: 'prev,next today',

            center: 'title',

            right: 'dayGridMonth,timeGridWeek,timeGridDay'

        },
        slotMinTime: '07:00:00',
        slotMaxTime: '22:00:00',

        // ==========================================
        // CLICK EN DÍA
        // ==========================================

        dateClick: function(info){
            if(!canManageCaseContent){
                return;
            }
            eventMode = 'create';
            eventId = null;

            $('#eventForm')[0].reset();

            $('#event_start_date').val(info.dateStr);
            $('#event_end_date').val(info.dateStr);

            // ==========================================
            // HORA ACTUAL PERÚ
            // ==========================================

            let now = new Date(
                new Date().toLocaleString(
                    'en-US',
                    {
                        timeZone: 'America/Lima'
                    }
                )
            );
            now = roundToNext15Minutes(now);
            let startTime = formatTime(now);
            let endTime = add60Minutes(startTime);
            $('#event_start_time').val(startTime);
            $('#event_end_time').val(endTime);

            $('#modalEvent').modal('show');

        },

        // ==========================================
        // CLICK EVENTO
        // ==========================================

        eventClick: function(info){

            if(!canManageCaseContent){
                return;
            }

            const event = info.event;

            eventMode = 'edit';

            eventId = event.id;

            $('#event_type').val(event.extendedProps.type);

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

            if(!canManageCaseContent){
                info.revert();
                return;
            }
            $.ajax({

                url: `/agenda/${info.event.id}`,

                method: 'PUT',

                data: {

                    _token: '{{ csrf_token() }}',

                    start_datetime: info.event.startStr,

                    end_datetime: info.event.endStr,

                    title: info.event.title,

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

            if(!canManageCaseContent){
                info.revert();
                return;
            }
            $.ajax({

                url: `/agenda/${info.event.id}`,

                method: 'PUT',

                data: {

                    _token: '{{ csrf_token() }}',

                    start_datetime: info.event.startStr,

                    end_datetime: info.event.endStr,

                    title: info.event.title,

                    type: info.event.type

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

            let type =
                info.event.extendedProps.type_label ?? '';

            let description =
                info.event.extendedProps.description ?? '';

            let location =
                info.event.extendedProps.location ?? '';

            let start =
                info.event.start
                    ? info.event.start.toLocaleString()
                    : '';

            let end =
                info.event.end
                    ? info.event.end.toLocaleString()
                    : '';

            let html = `
                <div class="text-start">

                    <strong>
                        ${type} - ${info.event.title}
                    </strong>

                    <hr class="my-1">

                    <div>
                        ${description}
                    </div>

                    <div class="mt-1">
                        📍 ${location}
                    </div>

                    <div class="mt-1">
                        🕒 ${start}
                    </div>

                </div>
            `;

            $(info.el).tooltip({

                title: html,

                html: true,

                placement: 'top',

                trigger: 'hover',

                container: 'body'

            });

        },

    });

    calendar.render();

    document.getElementById('agenda-tab').addEventListener('shown.bs.tab', function () {

        setTimeout(function () {

            calendar.updateSize();

        },100);

    });

});

</script>
@endpush

@push('styles')

<style>
.fc {

    font-size: 14px;

}

.fc .fc-toolbar-title {

    font-size: 1.3rem;
    font-weight: 600;

}

.fc .fc-button {

    background: #ffffff !important;
    border: 1px solid #dbe2ea !important;
    color: #334155 !important;

    border-radius: 10px !important;
    padding: 6px 14px !important;

    font-weight: 500;
    box-shadow: 0 1px 2px rgba(0,0,0,.04);

    transition: all .2s ease;

}

.fc .fc-button:hover {

    background: #f1f5f9 !important;
    border-color: #cbd5e1 !important;
    color: #0f172a !important;

}

.fc .fc-button.fc-button-active {

    background: #2563eb !important;
    border-color: #2563eb !important;
    color: #fff !important;

}

.fc .fc-button:focus {

    box-shadow: 0 0 0 .2rem rgba(37,99,235,.15) !important;

}

.fc .fc-daygrid-day {

    transition: background-color .2s;

}

.fc .fc-daygrid-day:hover {

    background: #f8f9fa;

}

.fc .fc-event {

    background: #3b82f6;
    border: none !important;

    color: white;

    border-radius: 8px !important;

    padding: 3px 7px;

    font-size: 12px;
    font-weight: 500;

    box-shadow: 0 1px 2px rgba(0,0,0,.10);

}

.fc .fc-event:hover {

    transform: translateY(-1px);

    transition: .15s;

}

.fc-event-title {

    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;

}

.fc .fc-day-today {

    background: #eff6ff !important;
    position: relative;

}

.fc .fc-day-today::after {

    content: '';
    position: absolute;

    top: 0;
    left: 0;

    width: 4px;
    height: 100%;

    background: #2563eb;

}

.fc-theme-standard td,
.fc-theme-standard th {

    border-color: #ebeef2;

}

.fc-scrollgrid {

    border-radius: 12px;
    overflow: hidden;

}

</style>
@endpush