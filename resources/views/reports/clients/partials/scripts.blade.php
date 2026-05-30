@push('scripts')

<script>

let clientsTable;

let chartIncome;
let chartProfit;
let chartSpecialties;
let chartServices;
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

    if(chartSpecialties) chartSpecialties.destroy();

    if(chartServices) chartServices.destroy();

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
    | Top ingresos
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
    | Top utilidad
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
    | Especialidades
    |--------------------------------------------------------------------------
    */

    chartSpecialties = new ApexCharts(

        document.querySelector('#chart_specialties'),

        {

            chart: {

                type: 'donut',

                height: 320

            },

            labels:
                charts.specialties.labels,

            series:
                charts.specialties.values

        }

    );

    chartSpecialties.render();

    /*
    |--------------------------------------------------------------------------
    | Servicios
    |--------------------------------------------------------------------------
    */

    chartServices = new ApexCharts(

        document.querySelector('#chart_services'),

        {

            chart: {

                type: 'pie',

                height: 320

            },

            labels:
                charts.services.labels,

            series:
                charts.services.values

        }

    );

    chartServices.render();

    /*
    |--------------------------------------------------------------------------
    | Estados
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
    if (clientsTable) {

        clientsTable.destroy();

    }

    clientsTable = $('#table-clients')
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
                        'Reporte de Clientes',

                    sheetName:
                        'Hoja1',

                    filename: function(){

                        let now = new Date();

                        return 'Reporte_Clientes_' +

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
                        'Reporte de Clientes'

                }

            ],

            language: {

                url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'

            },

            ajax: {

                url:
                    "{{ route('reports.clients.datatable') }}",

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

                    d.service_type =
                        $('#service_type').val();

                },

                dataSrc: function(json){

                    /*
                    |--------------------------------------------------------------------------
                    | KPIs
                    |--------------------------------------------------------------------------
                    */

                    $('#kpi_total_clients')
                        .html(
                            json.summary.total_clients
                        );

                    $('#kpi_active_clients')
                        .html(
                            json.summary.active_clients
                        );

                    $('#kpi_clients_with_debt')
                        .html(
                            json.summary.clients_with_debt
                        );

                    $('#kpi_inactive_clients')
                        .html(
                            json.summary.inactive_clients
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

                    $('#kpi_best_client')
                        .html(

                            json.summary.best_client_name

                            +

                            '<br>'

                            +

                            'S/ '

                            +

                            parseFloat(

                                json.summary.best_client_profit

                            ).toFixed(2)

                        );

                    $('#kpi_top_cases_client')
                        .html(

                            json.summary.top_cases_client_name

                            +

                            '<br>'

                            +

                            json.summary.top_cases_client_total

                            +

                            ' casos'

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
                    data: 'client_name'
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
                    data: 'debt',
                    className: 'text-end'
                },

                {
                    data: 'last_communication'
                },

                {
                    data: 'days_without_communication',
                    className: 'text-center'
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