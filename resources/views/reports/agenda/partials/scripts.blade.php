@push('scripts')

<script>

let agendaTable;

let chartTypes;
let chartMonths;
let chartLawyers;
let chartSpecialties;

/*
|--------------------------------------------------------------------------
| Destroy Charts
|--------------------------------------------------------------------------
*/

function destroyCharts()
{
    if(chartTypes) chartTypes.destroy();

    if(chartMonths) chartMonths.destroy();

    if(chartLawyers) chartLawyers.destroy();

    if(chartSpecialties) chartSpecialties.destroy();
}

/*
|--------------------------------------------------------------------------
| Charts
|--------------------------------------------------------------------------
*/

function loadCharts(charts)
{
    destroyCharts();

    /*
    |--------------------------------------------------------------------------
    | Eventos por Tipo
    |--------------------------------------------------------------------------
    */

    chartTypes = new ApexCharts(

        document.querySelector('#chart_types'),

        {

            chart: {

                type: 'donut',

                height: 320

            },

            labels:
                charts.types.labels,

            series:
                charts.types.values

        }

    );

    chartTypes.render();

    /*
    |--------------------------------------------------------------------------
    | Eventos por Mes
    |--------------------------------------------------------------------------
    */

    chartMonths = new ApexCharts(

        document.querySelector('#chart_months'),

        {

            chart: {

                type: 'bar',

                height: 320

            },

            series: [

                {

                    name: 'Eventos',

                    data:
                        charts.months.values

                }

            ],

            xaxis: {

                categories:
                    charts.months.labels

            }

        }

    );

    chartMonths.render();

    /*
    |--------------------------------------------------------------------------
    | Eventos por Abogado
    |--------------------------------------------------------------------------
    */

    chartLawyers = new ApexCharts(

        document.querySelector('#chart_lawyers'),

        {

            chart: {

                type: 'bar',

                height: 320

            },

            plotOptions: {

                bar: {

                    horizontal: true

                }

            },

            series: [

                {

                    name: 'Eventos',

                    data:
                        charts.lawyers.values

                }

            ],

            xaxis: {

                categories:
                    charts.lawyers.labels

            }

        }

    );

    chartLawyers.render();

    /*
    |--------------------------------------------------------------------------
    | Eventos por Especialidad
    |--------------------------------------------------------------------------
    */

    chartSpecialties = new ApexCharts(

        document.querySelector('#chart_specialties'),

        {

            chart: {

                type: 'pie',

                height: 320

            },

            labels:
                charts.specialties.labels,

            series:
                charts.specialties.values

        }

    );

    chartSpecialties.render();
}

/*
|--------------------------------------------------------------------------
| Datatable
|--------------------------------------------------------------------------
*/

function loadTable()
{
    if (agendaTable) {

        agendaTable.destroy();

    }

    agendaTable = $('#table-agenda')
        .DataTable({

            processing: true,

            destroy: true,

            responsive: true,

            pageLength: 25,

            dom: 'Bfrtip',

            buttons: [

                {

                    extend: 'excelHtml5',

                    title:
                        'Reporte de Agenda',

                    sheetName:
                        'Hoja1',

                    filename: function(){

                        let now = new Date();

                        return 'Reporte_Agenda_' +

                            now.getFullYear() +

                            '-' +

                            String(
                                now.getMonth()+1
                            ).padStart(2,'0') +

                            '-' +

                            String(
                                now.getDate()
                            ).padStart(2,'0') +

                            '_' +

                            String(
                                now.getHours()
                            ).padStart(2,'0') +

                            '-' +

                            String(
                                now.getMinutes()
                            ).padStart(2,'0');

                    }

                },

                {

                    extend: 'print',

                    title:
                        'Reporte de Agenda'

                }

            ],

            language: {

                url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'

            },

            ajax: {

                url:
                    "{{ route('reports.agenda.datatable') }}",

                data: function(d){

                    d.date_from =
                        $('#date_from').val();

                    d.date_to =
                        $('#date_to').val();

                    d.establishment_id =
                        $('#establishment_id').val();

                    d.specialty_id =
                        $('#specialty_id').val();

                    d.lawyer_id =
                        $('#lawyer_id').val();

                    d.type =
                        $('#type').val();

                },

                dataSrc: function(json){

                    /*
                    |--------------------------------------------------------------------------
                    | KPIs
                    |--------------------------------------------------------------------------
                    */

                    $('#kpi_total_events')
                        .html(
                            json.summary.total_events
                        );

                    $('#kpi_events_today')
                        .html(
                            json.summary.events_today
                        );

                    $('#kpi_upcoming_7_days')
                        .html(
                            json.summary.upcoming_7_days
                        );

                    $('#kpi_upcoming_deadlines')
                        .html(
                            json.summary.upcoming_deadlines
                        );

                    $('#kpi_cases_without_next_event')
                        .html(
                            json.summary.cases_without_next_event
                        );

                    $('#kpi_events_without_case')
                        .html(
                            json.summary.events_without_case
                        );

                    $('#kpi_top_lawyer')
                        .html(

                            json.summary.top_lawyer_name

                            +

                            '<br>'

                            +

                            json.summary.top_lawyer_events

                            +

                            ' eventos'

                        );

                    $('#kpi_next_event')
                        .html(

                            json.summary.next_event_title

                            +

                            '<br>'

                            +

                            json.summary.next_event_date

                        );

                    /*
                    |--------------------------------------------------------------------------
                    | Charts
                    |--------------------------------------------------------------------------
                    */

                    loadCharts(
                        json.charts
                    );

                    return json.data;

                }

            },

            columns: [

                {
                    data: 'date'
                },

                {
                    data: 'title'
                },

                {
                    data: 'type'
                },

                {
                    data: 'case_title'
                },

                {
                    data: 'client_name'
                },

                {
                    data: 'lawyer_name'
                },

                {
                    data: 'location'
                },

                {
                    data: 'creator'
                }

            ]

        });
}

/*
|--------------------------------------------------------------------------
| Init
|--------------------------------------------------------------------------
*/

loadTable();

/*
|--------------------------------------------------------------------------
| Buscar
|--------------------------------------------------------------------------
*/

$('#btn-search').on(

    'click',

    function(){

        loadTable();

    }

);

</script>

@endpush