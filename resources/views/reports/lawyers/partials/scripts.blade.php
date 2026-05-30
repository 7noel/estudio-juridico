@push('scripts')

<script>

let lawyersTable;

let chartIncome;
let chartProfit;
let chartCases;
let chartActivities;
let chartStatus;

/*
|--------------------------------------------------------------------------
| Destroy Charts
|--------------------------------------------------------------------------
*/

function destroyCharts()
{
    if(chartIncome) chartIncome.destroy();

    if(chartProfit) chartProfit.destroy();

    if(chartCases) chartCases.destroy();

    if(chartActivities) chartActivities.destroy();

    if(chartStatus) chartStatus.destroy();
}

/*
|--------------------------------------------------------------------------
| Load Charts
|--------------------------------------------------------------------------
*/

function loadCharts(charts)
{
    destroyCharts();

    /*
    |--------------------------------------------------------------------------
    | Ranking ingresos
    |--------------------------------------------------------------------------
    */

    chartIncome = new ApexCharts(

        document.querySelector('#chart_income'),

        {

            chart: {

                type: 'bar',

                height: 350

            },

            plotOptions: {

                bar: {

                    horizontal: true

                }

            },

            series: [

                {

                    name: 'Ingresos',

                    data:
                        charts.income.values

                }

            ],

            xaxis: {

                categories:
                    charts.income.labels

            }

        }

    );

    chartIncome.render();

    /*
    |--------------------------------------------------------------------------
    | Ranking utilidad
    |--------------------------------------------------------------------------
    */

    chartProfit = new ApexCharts(

        document.querySelector('#chart_profit'),

        {

            chart: {

                type: 'bar',

                height: 350

            },

            plotOptions: {

                bar: {

                    horizontal: true

                }

            },

            series: [

                {

                    name: 'Utilidad',

                    data:
                        charts.profit.values

                }

            ],

            xaxis: {

                categories:
                    charts.profit.labels

            }

        }

    );

    chartProfit.render();

    /*
    |--------------------------------------------------------------------------
    | Casos por abogado
    |--------------------------------------------------------------------------
    */

    chartCases = new ApexCharts(

        document.querySelector('#chart_cases'),

        {

            chart: {

                type: 'pie',

                height: 320

            },

            labels:
                charts.cases.labels,

            series:
                charts.cases.values

        }

    );

    chartCases.render();

    /*
    |--------------------------------------------------------------------------
    | Actividades por abogado
    |--------------------------------------------------------------------------
    */

    chartActivities = new ApexCharts(

        document.querySelector('#chart_activities'),

        {

            chart: {

                type: 'donut',

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
    | Casos por estado
    |--------------------------------------------------------------------------
    */

    chartStatus = new ApexCharts(

        document.querySelector('#chart_status'),

        {

            chart: {

                type: 'pie',

                height: 320

            },

            labels:
                charts.status.labels,

            series:
                charts.status.values

        }

    );

    chartStatus.render();
}

/*
|--------------------------------------------------------------------------
| Datatable
|--------------------------------------------------------------------------
*/

function loadTable()
{
    if (lawyersTable) {

        lawyersTable.destroy();

    }

    lawyersTable = $('#table-lawyers')
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
                        'Reporte de Abogados',

                    sheetName:
                        'Hoja1',

                    filename: function(){

                        let now = new Date();

                        return 'Reporte_Abogados_' +

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
                        'Reporte de Abogados'

                }

            ],

            language: {

                url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'

            },

            ajax: {

                url:
                    "{{ route('reports.lawyers.datatable') }}",

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

                    $('#kpi_total_lawyers')
                        .html(
                            json.summary.total_lawyers
                        );

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

                    $('#kpi_activities')
                        .html(
                            json.summary.activities
                        );

                    $('#kpi_events')
                        .html(
                            json.summary.events
                        );

                    $('#kpi_income')
                        .html(
                            'S/ ' +
                            parseFloat(
                                json.summary.income
                            ).toFixed(2)
                        );

                    $('#kpi_profit')
                        .html(
                            'S/ ' +
                            parseFloat(
                                json.summary.profit
                            ).toFixed(2)
                        );

                    $('#kpi_inactive_cases')
                        .html(
                            json.summary.inactive_cases
                        );

                    $('#kpi_avg_activities')
                        .html(
                            json.summary.avg_activities
                        );

                    $('#kpi_best_lawyer')
                        .html(

                            json.summary.best_lawyer_name

                            +

                            '<br>'

                            +

                            'S/ '

                            +

                            parseFloat(

                                json.summary.best_lawyer_profit

                            ).toFixed(2)

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
                    data: 'lawyer_name'
                },

                {
                    data: 'cases',
                    className: 'text-center'
                },

                {
                    data: 'active_cases',
                    className: 'text-center'
                },

                {
                    data: 'closed_cases',
                    className: 'text-center'
                },

                {
                    data: 'activities',
                    className: 'text-center'
                },

                {
                    data: 'events',
                    className: 'text-center'
                },

                {
                    data: 'inactive_cases',
                    className: 'text-center'
                },

                {
                    data: 'avg_activities',
                    className: 'text-end'
                },

                {
                    data: 'income',
                    className: 'text-end'
                },

                {
                    data: 'expense',
                    className: 'text-end'
                },

                {
                    data: 'profit',
                    className: 'text-end'
                },

                {
                    data: 'margin',
                    className: 'text-end'
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