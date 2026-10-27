<script>


let activityForm = $('#form-activity');

let activitySubtypes = {

    legal: @json(config('options.activity_types')),

    communication: @json(config('options.communication_types')),

    judicial_progress: @json(config('options.judicial_progress_types')),

    note: {}

};

// =====================================================
// SUBTIPOS
// =====================================================

function loadActivitySubtypes(type, selected = null){

    let options = activitySubtypes[type] || {};

    let html = '<option value=""> Seleccionar </option>';

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

    let text = $(this).find('option:selected').text().trim();

    if(!$('[name="title"]').val()){

        // $('[name="title"]').val(text);

    }

    // 🔥 titulo evento
    //if(!$('#activity_event_title').val()){

    //    $('#activity_event_title').val(text);

    //}

});

$('#activity_event_type').change(function(){

    let text = $(this).find('option:selected').text().trim();
    // 🔥 titulo evento
    if(!$('#activity_event_title').val()){

        // $('#activity_event_title').val(text);

    }

});

// =====================================================
// ABRIR NUEVA ACTIVIDAD
// =====================================================

$('#btnAddActivity').click(function(){


    $('#activity_id').val('');

    activityForm[0].reset();

    $('#modalTitle').text('Agregar actividad');

    // 🔥 Preseleccionar tipo según filtro activo
    let activeFilter = $('#activityFilters .btn.active').data('filter');

    if (activeFilter && activeFilter !== 'all') {
        $('#activity_type').val(activeFilter);
    }

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
    $('#activity_event_type').val('');
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

$(document).on('change', '#activity_event_start_time', function(){
    let startTime = $(this).val();
    $('#activity_event_end_time').val(
        add60Minutes(startTime)
    );
});

$(document).on('change', '#activity_event_start_date', function(){
    $('#activity_event_end_date').val( $(this).val() );
});

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

            let agendaType =
                btn.data('agenda-type');

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

            $('#activity_event_type').val(
                agendaType ?? ''
            );

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

activityForm.submit(function(e) {

    e.preventDefault();

    let id = $('#activity_id').val();

    let url = id
        ? `/activities/${id}`
        : `/cases/${caseId}/activities`;

    let method = id
        ? 'PUT'
        : 'POST';

    // =====================================
    // DETERMINAR SI TIENE EVENTO DE AGENDA
    // =====================================

    let hasAgendaEvent = false;

    if (id) {
        hasAgendaEvent =
            $('.btn-edit-activity[data-id="' + id + '"]')
                .data('has-event') == 1;
    }

    let createAgendaEvent =
        $('#create_agenda_event').is(':checked');

    let shouldSendAgenda =
        createAgendaEvent || hasAgendaEvent;

    // =====================================
    // OBTENER FECHAS
    // =====================================

    let startDate = $('#activity_event_start_date').val();
    let startTime = $('#activity_event_start_time').val();

    let endDate = $('#activity_event_end_date').val();
    let endTime = $('#activity_event_end_time').val();

    // =====================================
    // VALIDAR FECHAS SI CORRESPONDE
    // =====================================

    if (shouldSendAgenda) {

        if (!startDate || !startTime || !endDate || !endTime) {
            alert('Debes completar la fecha y hora de inicio y fin del evento.');
            return;
        }

        // Formato compatible con MySQL y Laravel:
        // YYYY-MM-DD HH:mm:ss

        let start_datetime =
            `${startDate} ${startTime}:00`;

        let end_datetime =
            `${endDate} ${endTime}:00`;

        // Comparar fechas completas.
        // Este formato ISO ordena correctamente año, mes, día y hora.

        let startComparable =
            `${startDate}T${startTime}:00`;

        let endComparable =
            `${endDate}T${endTime}:00`;

        if (endComparable <= startComparable) {
            alert('La fecha y hora de fin debe ser posterior a la fecha y hora de inicio.');
            return;
        }

    }

    // =====================================
    // CONSTRUIR DATOS DE AGENDA
    // =====================================

    let startDateTime = null;
    let endDateTime = null;

    if (shouldSendAgenda) {

        startDateTime =
            `${startDate} ${startTime}:00`;

        endDateTime =
            `${endDate} ${endTime}:00`;

    }

    // =====================================
    // ENVIAR AJAX
    // =====================================

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
                $('[name="activity_at"]').val() || null,

            // =================================
            // AGENDA
            // =================================

            create_agenda_event:
                createAgendaEvent ? 1 : 0,

            agenda_type:
                shouldSendAgenda
                    ? $('#activity_event_type').val() || null
                    : null,

            agenda_title:
                shouldSendAgenda
                    ? $('#activity_event_title').val() || null
                    : null,

            agenda_description:
                shouldSendAgenda
                    ? $('#activity_event_description').val() || null
                    : null,

            agenda_start_datetime:
                startDateTime,

            agenda_end_datetime:
                endDateTime,

            agenda_location:
                shouldSendAgenda
                    ? $('#activity_event_location').val() || null
                    : null,

        },

        success: function() {

            location.reload();

        },

        error: function(xhr) {

            console.error('Error al guardar actividad:', xhr.responseJSON || xhr.responseText);

            if (xhr.status === 422 && xhr.responseJSON?.errors) {

                let errors = Object.values(xhr.responseJSON.errors)
                    .flat()
                    .join('\n');

                alert(errors);

            } else {

                alert('Error al guardar la actividad. Revisa la consola para más detalles.');

            }

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

/* ==========================================================
 * Filtro de actividades
 * ==========================================================*/
$(document).on(
    'click',
    '#activityFilters .btn',
    function () {

        $('#activityFilters .btn')
            .removeClass('btn-primary active')
            .addClass('btn-outline-secondary');

        $(this)
            .removeClass('btn-outline-secondary')
            .addClass('btn-primary active');

        const filter = $(this).data('filter');

        if (filter === 'all') {

            $('.activity-item').show();

            return;
        }

        $('.activity-item').hide();

        $('.activity-item[data-type="' + filter + '"]').show();

    }
);

// =====================================================
// MOSTRAR / OCULTAR DESCRIPCIÓN
// =====================================================

$(document).on(
    'click',
    '.activity-title',
    function () {

        const description = $(this)
            .closest('td')
            .find('.activity-description');

        if (!description.length) {
            return;
        }

        description.stop(true, true).slideToggle(180);

        $(this)
            .find('.activity-arrow')
            .toggleClass('bi-caret-right-fill')
            .toggleClass('bi-caret-down-fill');

    }
);

</script>
