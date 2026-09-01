@push('scripts')

<style>

.btn-icon {
    width: 32px;
    height: 32px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}

.btn-icon i {
    font-size: 0.95rem;
    line-height: 1;
}

</style>

<script>

let activityTable;

let typeColors = {
    'legal': 'primary',
    'judicial_progress': 'success',
    'communication': 'info',
    'note': 'secondary'
};

// =====================================================
// INIT
// =====================================================

$(function(){

    initDataTable();

    loadReport();

    $('#btn-search').on('click', function(){
        loadReport();
    });

    $('.quick-range').on('click', function(){
        applyQuickRange($(this).data('range'));
        loadReport();
    });

    $('#btn-toggle-types').on('click', function(){

        let allChecked = $('.activity-type:checked').length > 0;

        $('.activity-type').prop('checked', !allChecked);

        $(this).text(allChecked ? 'Seleccionar todas' : 'Quitar todas');

    });

});

// =====================================================
// DATATABLE
// =====================================================

function initDataTable()
{
    activityTable = $('#activityDetailTable').DataTable({

        processing: true,

        searching: true,

        paging: true,

        ordering: true,

        destroy: true,

        dom: 'Bfrtip',

        buttons: [

            {
                extend: 'excelHtml5',
                title: 'Detalle de Actividades de Abogados',
                exportOptions: {
                    columns: ':not(.col-actions)'
                }
            },

            {
                extend: 'print',
                title: 'Detalle de Actividades de Abogados',
                exportOptions: {
                    columns: ':not(.col-actions)'
                }
            }

        ],

        language: {

            url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json'

        },

        columns: [

            { data: 'activity_at' },

            {
                data: null,
                orderable: false,
                render: function(data){

                    let color = typeColors[data.type] || 'dark';

                    return `
                        <span class="badge bg-${color} text-white">
                            ${data.type_label}
                        </span>
                        <div>
                            <small class="text-muted">
                                ${data.subtype}
                            </small>
                        </div>
                    `;
                }
            },

            { data: 'title' },

            { data: 'case_lawyer' },

            { data: 'case_number' },

            { data: 'client_name' },

            { data: 'case_title' },

            {
                data: null,
                orderable: false,
                className: 'col-actions text-center',
                render: function(data, type, row){

                    if(type === 'display'){

                        return `

                            <div class="d-flex justify-content-center gap-2">

                                <button
                                    class="btn btn-sm btn-icon btn-outline-primary btn-detail"
                                    data-id="${row.id}"
                                    title="Ver detalle"
                                >
                                    <i class="bi bi-eye"></i>
                                </button>

                                <a
                                    class="btn btn-sm btn-icon btn-outline-secondary"
                                    href="${row.case_url || '#'}"
                                    target="_blank"
                                    title="Abrir caso"
                                >
                                    <i class="bi bi-box-arrow-up-right"></i>
                                </a>

                            </div>

                        `;

                    }

                    return '';
                }
            }

        ]

    });

    $('#activityDetailTable').on('click', '.btn-detail', function(){

        let rowData = activityTable.row($(this).closest('tr')).data();

        openDetailModal(rowData);

    });
}

// =====================================================
// LOAD REPORT
// =====================================================

function loadReport()
{
    let types = [];

    $('.activity-type:checked').each(function(){
        types.push($(this).val());
    });

    if(types.length === 0){
        types = ['__none__'];
    }

    $.ajax({

        url: "{{ route('reports.activity-detail.datatable') }}",

        type: "GET",

        data: {

            lawyer_id: $('#lawyer_id').val(),

            establishment_id: $('#establishment_id').val(),

            date_start: $('#date_start').val(),

            date_end: $('#date_end').val(),

            types: types

        },

        success: function(response){

            loadSummary(response.summary);

            loadRows(response.data);

        },

        error: function(xhr){

            console.error(xhr);

            alert('Ocurrió un error al cargar el reporte.');

        }

    });
}

// =====================================================
// SUMMARY
// =====================================================

function loadSummary(summary)
{
    $('#kpiTotal').text(summary.total);

    $.each(summary.by_type, function(key, value){

        $('#kpiType_' + key).text(value);

    });
}

// =====================================================
// ROWS
// =====================================================

function loadRows(rows)
{
    activityTable.clear();

    activityTable.rows.add(rows);

    activityTable.draw();
}

// =====================================================
// MODAL DETALLE
// =====================================================

function openDetailModal(row)
{
    $('#mdActivityAt').text(row.activity_at);

    $('#mdType').text(row.type_label);

    $('#mdSubtype').text(row.subtype);

    $('#mdUserName').text(row.user_name);

    $('#mdTitle').text(row.title);

    $('#mdDescription').text(row.description || '-');

    let caseInfo = `
        <div>
            <span class="fw-semibold">Nº caso:</span>
            ${row.case_number}
        </div>
        <div>
            <span class="fw-semibold">Título:</span>
            ${row.case_title}
        </div>
        <div>
            <span class="fw-semibold">Cliente:</span>
            ${row.client_name}
        </div>
        <div>
            <span class="fw-semibold">Abogado del caso:</span>
            ${row.case_lawyer}
        </div>
    `;

    $('#mdCaseInfo').html(caseInfo);

    if(row.agenda_event){

        let agenda = row.agenda_event;

        $('#mdAgendaInfo').html(`
            <div>
                <span class="fw-semibold">Tipo:</span>
                ${agenda.type_label}
            </div>
            <div>
                <span class="fw-semibold">Título:</span>
                ${agenda.title || '-'}
            </div>
            <div>
                <span class="fw-semibold">Descripción:</span>
                ${agenda.description || '-'}
            </div>
            <div>
                <span class="fw-semibold">Inicio:</span>
                ${agenda.start_datetime || '-'}
            </div>
            <div>
                <span class="fw-semibold">Fin:</span>
                ${agenda.end_datetime || '-'}
            </div>
            <div>
                <span class="fw-semibold">Lugar:</span>
                ${agenda.location || '-'}
            </div>
        `);

        $('#mdAgendaBlock').show();

    } else {

        $('#mdAgendaBlock').hide();

    }

    $('#mdOpenCase')
        .attr('href', row.case_url || '#')
        .toggleClass('disabled', !row.case_url);

    $('#modalActivityDetail').modal('show');
}

// =====================================================
// QUICK RANGES
// =====================================================

function applyQuickRange(range)
{
    let today = new Date();

    let from;
    let to;

    if(range === 'today')
    {
        from = formatDate(today);
        to = formatDate(today);
    }

    if(range === 'yesterday')
    {
        let yesterday = new Date();

        yesterday.setDate(yesterday.getDate() - 1);

        from = formatDate(yesterday);
        to = formatDate(yesterday);
    }

    if(range === 'week')
    {
        let firstDay = new Date(today);

        firstDay.setDate(today.getDate() - today.getDay());

        from = formatDate(firstDay);
        to = formatDate(today);
    }

    if(range === 'month')
    {
        let firstDay = new Date(
            today.getFullYear(),
            today.getMonth(),
            1
        );

        from = formatDate(firstDay);
        to = formatDate(today);
    }

    $('#date_start').val(from);

    $('#date_end').val(to);
}

// =====================================================
// HELPERS
// =====================================================

function formatDate(date)
{
    let month = String(date.getMonth() + 1).padStart(2, '0');

    let day = String(date.getDate()).padStart(2, '0');

    return `${date.getFullYear()}-${month}-${day}`;
}

// @@PART2@@

</script>

@endpush
