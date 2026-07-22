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

            agenda_type:
                $('#activity_event_type').val(),

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
