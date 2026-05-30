@push('scripts')

<script>

let adminEventMode = 'create';

let adminEventId = null;

function pad(num)
{
    return String(num).padStart(2, '0');
}

function formatDate(date)
{
    return date.getFullYear()
        + '-'
        + pad(date.getMonth() + 1)
        + '-'
        + pad(date.getDate());
}

function formatTime(date)
{
    return pad(date.getHours())
        + ':'
        + pad(date.getMinutes());
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
    console.log(`now: ${date}, hora: ${h}:${m}`)
    return `${h}:${m}`;
}

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

$('#admin_event_start_time')
    .change(function(){

        let startTime =
            $(this).val();

        let endTime =
            add60Minutes(startTime);

        $('#admin_event_end_time')
            .val(endTime);

    });

generateTimeOptions(
    '#admin_event_start_time'
);

generateTimeOptions(
    '#admin_event_end_time'
);

document.addEventListener(
    'DOMContentLoaded',
    function () {

        let calendarEl =
            document.getElementById(
                'calendar'
            );

        let calendar =
            new FullCalendar.Calendar(
                calendarEl,
                {

                    locale: 'es',

                    height: 650,

                    selectable: true,

                    editable: true,

                    initialView: 'dayGridMonth',

                    slotDuration: '00:15:00',

                    snapDuration: '00:15:00',

                    slotMinTime: '07:00:00',

                    slotMaxTime: '22:00:00',

                    buttonText: {

                        today: 'Hoy',

                        month: 'Mes',

                        week: 'Semana',

                        day: 'Día',

                    },

                    headerToolbar: {

                        left:
                            'prev,next today',

                        center:
                            'title',

                        right:
                            'dayGridMonth,timeGridWeek,timeGridDay'

                    },

                    /*
                    |--------------------------------------------------------------------------
                    | Fuentes AJAX
                    |--------------------------------------------------------------------------
                    */

                    eventSources: [

                        {

                            url:
                                '/dashboard/legal-events',

                            method:
                                'GET'

                        },

                        {

                            url:
                                '/dashboard/agenda/events',

                            method:
                                'GET'

                        }

                    ],

                    /*
                    |--------------------------------------------------------------------------
                    | Crear evento administrativo
                    |--------------------------------------------------------------------------
                    */

                    dateClick: function(info){

                        adminEventMode =
                            'create';

                        adminEventId =
                            null;

                        $('#adminEventForm')[0]
                            .reset();

                        $('#btnDeleteAdminEvent')
                            .hide();

                        $('#admin_event_start_date')
                            .val(info.dateStr);

                        $('#admin_event_end_date')
                            .val(info.dateStr);

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
                        
                        $('#admin_event_start_time')
                            .val(startTime);

                        $('#admin_event_end_time')
                            .val(endTime);

                        $('#modalAdminEvent')
                            .modal('show');

                    },

                    /*
                    |--------------------------------------------------------------------------
                    | Click evento
                    |--------------------------------------------------------------------------
                    */

                    eventClick: function(info){

                        let props =
                            info.event.extendedProps;

                        /*
                        |--------------------------------------------------------------------------
                        | Evento Jurídico
                        |--------------------------------------------------------------------------
                        */

                        if(
                            props.is_legal_event
                        ){

                            let html = '';

                            html +=
                                '<div class="text-start">';

                            html +=
                                '<p><strong>Caso:</strong> '
                                +
                                (
                                    props.case_title
                                    ?? '-'
                                )
                                +
                                '</p>';

                            html +=
                                '<p><strong>Cliente:</strong> '
                                +
                                (
                                    props.client_name
                                    ?? '-'
                                )
                                +
                                '</p>';

                            html +=
                                '<p><strong>Abogado:</strong> '
                                +
                                (
                                    props.lawyer_name
                                    ?? '-'
                                )
                                +
                                '</p>';

                            html +=
                                '<p><strong>Descripción:</strong><br>'
                                +
                                (
                                    props.description
                                    ?? ''
                                )
                                +
                                '</p>';

                            html +=
                                '</div>';

                            Swal.fire({

                                title:
                                    info.event.title,

                                html:
                                    html,

                                icon:
                                    'info',

                                showCancelButton:
                                    true,

                                confirmButtonText:
                                    'Ir al Caso',

                                cancelButtonText:
                                    'Cerrar'

                            }).then(
                                (result) => {

                                    if(
                                        result.isConfirmed
                                    ){

                                        window.location.href =
                                            props.case_url;

                                    }

                                }
                            );

                            return;

                        }

                        /*
                        |--------------------------------------------------------------------------
                        | Evento Administrativo
                        |--------------------------------------------------------------------------
                        */

                        adminEventMode =
                            'edit';

                        adminEventId =
                            info.event.id;

                        $('#btnDeleteAdminEvent')
                            .show();

                        $('#admin_event_type')
                            .val(props.type);

                        $('#admin_event_title')
                            .val(info.event.title);

                        $('#admin_event_description')
                            .val(
                                props.description
                            );

                        $('#admin_event_location')
                            .val(
                                props.location
                            );

                        let start =
                            info.event.start;

                        let end =
                            info.event.end;

                        $('#admin_event_start_date')
                            .val(
                                formatDate(start)
                            );

                        $('#admin_event_start_time')
                            .val(
                                formatTime(start)
                            );

                        if(end){

                            $('#admin_event_end_date')
                                .val(
                                    formatDate(end)
                                );

                            $('#admin_event_end_time')
                                .val(
                                    formatTime(end)
                                );

                        }

                        $('#modalAdminEvent')
                            .modal('show');

                    },

                    /*
                    |--------------------------------------------------------------------------
                    | Drag
                    |--------------------------------------------------------------------------
                    */

                    eventDrop: function(info){

                        if(
                            info.event.extendedProps
                                .is_legal_event
                        ){

                            info.revert();

                            return;

                        }

                        $.ajax({

                            url:
                                '/dashboard/agenda/'
                                +
                                info.event.id,

                            method:
                                'PUT',

                            data: {

                                _token:
                                    '{{ csrf_token() }}',

                                start_datetime:
                                    info.event.startStr,

                                end_datetime:
                                    info.event.endStr,

                            }

                        });

                    },

                    /*
                    |--------------------------------------------------------------------------
                    | Resize
                    |--------------------------------------------------------------------------
                    */

                    eventResize: function(info){

                        if(
                            info.event.extendedProps
                                .is_legal_event
                        ){

                            info.revert();

                            return;

                        }

                        $.ajax({

                            url:
                                '/dashboard/agenda/'
                                +
                                info.event.id,

                            method:
                                'PUT',

                            data: {

                                _token:
                                    '{{ csrf_token() }}',

                                start_datetime:
                                    info.event.startStr,

                                end_datetime:
                                    info.event.endStr,

                            }

                        });

                    }

                }
            );

        calendar.render();

        /*
        |--------------------------------------------------------------------------
        | Guardar
        |--------------------------------------------------------------------------
        */

        $('#btnSaveAdminEvent')
            .click(function(){

                let startDatetime =

                    $('#admin_event_start_date')
                        .val()

                    +

                    ' '

                    +

                    $('#admin_event_start_time')
                        .val()

                    +

                    ':00';

                let endDatetime =

                    $('#admin_event_end_date')
                        .val()

                    +

                    ' '

                    +

                    $('#admin_event_end_time')
                        .val()

                    +

                    ':00';

                let url =
                    adminEventMode === 'create'

                    ?

                    '/dashboard/agenda'

                    :

                    '/dashboard/agenda/'
                    +
                    adminEventId;

                let method =
                    adminEventMode === 'create'

                    ?

                    'POST'

                    :

                    'PUT';

                $.ajax({

                    url:
                        url,

                    method:
                        method,

                    data: {

                        _token:
                            '{{ csrf_token() }}',

                        type:
                            $('#admin_event_type')
                                .val(),

                        title:
                            $('#admin_event_title')
                                .val(),

                        description:
                            $('#admin_event_description')
                                .val(),

                        location:
                            $('#admin_event_location')
                                .val(),

                        start_datetime:
                            startDatetime,

                        end_datetime:
                            endDatetime,

                    },

                    success: function(){

                        $('#modalAdminEvent')
                            .modal('hide');

                        calendar.refetchEvents();

                    }

                });

            });

        /*
        |--------------------------------------------------------------------------
        | Eliminar
        |--------------------------------------------------------------------------
        */

        $('#btnDeleteAdminEvent')
            .click(function(){

                if(
                    !confirm(
                        '¿Eliminar evento?'
                    )
                ){
                    return;
                }

                $.ajax({

                    url:
                        '/dashboard/agenda/'
                        +
                        adminEventId,

                    method:
                        'DELETE',

                    data: {

                        _token:
                            '{{ csrf_token() }}'

                    },

                    success: function(){

                        $('#modalAdminEvent')
                            .modal('hide');

                        calendar.refetchEvents();

                    }

                });

            });

    }
);

/*
|--------------------------------------------------------------------------
| Casos por Estado
|--------------------------------------------------------------------------
*/

new ApexCharts(

    document.querySelector(
        '#casesByStatusChart'
    ),

    {

        chart: {

            type: 'donut',

            height: 350

        },

        labels:

            {!! json_encode(
                $casesByStatus->keys()
            ) !!},

        series:

            {!! json_encode(
                $casesByStatus->values()
            ) !!}

    }

).render();

/*
|--------------------------------------------------------------------------
| Casos por Especialidad
|--------------------------------------------------------------------------
*/

new ApexCharts(

    document.querySelector(
        '#casesBySpecialtyChart'
    ),

    {

        chart: {

            type: 'bar',

            height: 350

        },

        series: [

            {

                name:
                    'Casos',

                data:

                    {!! json_encode(
                        $casesBySpecialty->values()
                    ) !!}

            }

        ],

        xaxis: {

            categories:

                {!! json_encode(
                    $casesBySpecialty->keys()
                ) !!}

        }

    }

).render();


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

}

.fc .fc-button:hover {

    background: #f1f5f9 !important;

}

.fc .fc-button.fc-button-active {

    background: #2563eb !important;
    color: #fff !important;

}

.fc .fc-day-today {

    background: #eff6ff !important;

}

.fc .fc-event {

    border-radius: 8px !important;

}
</style>
@endpush