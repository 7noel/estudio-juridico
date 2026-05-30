@push('scripts')

<script>

let operationalTable;

let chartStatus;
let chartSpecialties;
let chartLawyers;
let chartActivities;
let chartEvents;

/*
|--------------------------------------------------------------------------
| Destroy Charts
|--------------------------------------------------------------------------
*/

function destroyCharts()
{
    if(chartStatus) chartStatus.destroy();
    if(chartSpecialties) chartSpecialties.destroy();
    if(chartLawyers) chartLawyers.destroy();
    if(chartActivities) chartActivities.destroy();
    if(chartEvents) chartEvents.destroy();
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
    | Estado
    |--------------------------------------------------------------------------
    */

    chartStatus = new ApexCharts(

        document.querySelector('#chart_status'),

        {

            chart: {

                type: 'donut',

                height: 320

            },

            labels:
                charts.status.labels,

            series:
                charts.status.values

        }

    );

    chartStatus.render();

    /*
    |--------------------------------------------------------------------------
    | Especialidades
    |--------------------------------------------------------------------------
    */

    chartSpecialties = new ApexCharts(

        document.querySelector('#chart_specialties'),

        {

            chart: {

                type: 'bar',

                height: 320

            },

            series: [

                {

                    name: 'Casos',

                    data:
                        charts.specialties.values

                }

            ],

            xaxis: {

                categories:
                    charts.specialties.labels

            }

        }

    );

    chartSpecialties.render();

    /*
    |--------------------------------------------------------------------------
    | Abogados
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

                    name: 'Casos',

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
    | Actividades
    |--------------------------------------------------------------------------
    */

    chartActivities = new ApexCharts(

        document.querySelector('#chart_activities'),

        {

            chart: {

                type: 'pie',

                height: 320

            },

            labels:
                charts.activities.labels,

            series:
                charts.activities.values

        }

    );

    chartActivities.render();

    /*
    |--------------------------------------------------------------------------
    | Eventos
    |--------------------------------------------------------------------------
    */

    chartEvents = new ApexCharts(

        document.querySelector('#chart_events'),

        {

            chart: {

                type: 'pie',

                height: 320

            },

            labels:
                charts.events.labels,

            series:
                charts.events.values

        }

    );

    chartEvents.render();
}

/*
|--------------------------------------------------------------------------
| Datatable
|--------------------------------------------------------------------------
*/

function loadTable()
{
    if (operationalTable) {

        operationalTable.destroy();

    }

    operationalTable = $('#table-operational')
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
                        'Reporte Operativo',

                    sheetName:
                        'Hoja1',

                    filename: function(){

                        let now = new Date();

                        return 'Reporte_Operativo_' +

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
                        'Reporte Operativo'

                }

            ],

            language: {

                url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'

            },

            ajax: {

                url:
                    "{{ route('reports.operational.datatable') }}",

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

                    d.status =
                        $('#status').val();

                },

                dataSrc: function(json){

                    /*
                    |--------------------------------------------------------------------------
                    | KPIs
                    |--------------------------------------------------------------------------
                    */

                    $('#kpi_total_cases')
                        .html(
                            json.summary.total_cases
                        );

                    $('#kpi_active_cases')
                        .html(
                            json.summary.active_cases
                        );

                    $('#kpi_closed_cases')
                        .html(
                            json.summary.closed_cases
                        );

                    $('#kpi_paused_cases')
                        .html(
                            json.summary.paused_cases
                        );

                    $('#kpi_activities')
                        .html(
                            json.summary.activities
                        );

                    $('#kpi_events')
                        .html(
                            json.summary.events
                        );

                    $('#kpi_without_recent_communication')
                        .html(
                            json.summary.without_recent_communication
                        );

                    $('#kpi_without_activities')
                        .html(
                            json.summary.without_activities
                        );

                    $('#kpi_without_future_events')
                        .html(
                            json.summary.without_future_events
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
                    data: 'case_title'
                },

                {
                    data: 'client'
                },

                {
                    data: 'specialty'
                },

                {
                    data: 'lawyer'
                },

                {
                    data: 'status'
                },

                {
                    data: 'activities_count',
                    className: 'text-center'
                },

                {
                    data: 'last_activity'
                },

                {
                    data: 'next_event'
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