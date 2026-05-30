@push('scripts')

<script>

let profitabilityTable;

let chartTopCases;
let chartLawyers;
let chartSpecialties;
let chartServices;

/*
|--------------------------------------------------------------------------
| Destroy Charts
|--------------------------------------------------------------------------
*/

function destroyCharts()
{
    if(chartTopCases) chartTopCases.destroy();
    if(chartLawyers) chartLawyers.destroy();
    if(chartSpecialties) chartSpecialties.destroy();
    if(chartServices) chartServices.destroy();
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
    | Top Casos
    |--------------------------------------------------------------------------
    */

    chartTopCases = new ApexCharts(

        document.querySelector(
            '#chart_top_cases'
        ),

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
                        charts.top_cases.values

                }

            ],

            xaxis: {

                categories:
                    charts.top_cases.labels

            }

        }

    );

    chartTopCases.render();

    /*
    |--------------------------------------------------------------------------
    | Abogados
    |--------------------------------------------------------------------------
    */

    chartLawyers = new ApexCharts(

        document.querySelector(
            '#chart_lawyers'
        ),

        {

            chart: {

                type: 'bar',

                height: 350

            },

            series: [

                {

                    name: 'Utilidad',

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
    | Especialidades
    |--------------------------------------------------------------------------
    */

    chartSpecialties = new ApexCharts(

        document.querySelector(
            '#chart_specialties'
        ),

        {

            chart: {

                type: 'donut',

                height: 350

            },

            series:
                charts.specialties.values,

            labels:
                charts.specialties.labels

        }

    );

    chartSpecialties.render();

    /*
    |--------------------------------------------------------------------------
    | Servicios
    |--------------------------------------------------------------------------
    */

    chartServices = new ApexCharts(

        document.querySelector(
            '#chart_services'
        ),

        {

            chart: {

                type: 'pie',

                height: 350

            },

            series:
                charts.services.values,

            labels:
                charts.services.labels

        }

    );

    chartServices.render();
}

/*
|--------------------------------------------------------------------------
| Datatable
|--------------------------------------------------------------------------
*/

function loadTable()
{
    if (profitabilityTable) {

        profitabilityTable.destroy();

    }

    profitabilityTable = $('#table-profitability')
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
                        'Reporte de Rentabilidad',

                    sheetName:
                        'Hoja1',

                    filename: function(){

                        let now = new Date();

                        return 'Reporte_Rentabilidad_' +

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
                        'Reporte de Rentabilidad'

                }

            ],

            language: {

                url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'

            },

            ajax: {

                url:
                    "{{ route('reports.profitability.datatable') }}",

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

                    d.service_type =
                        $('#service_type').val();

                    d.status =
                        $('#status').val();

                },

                dataSrc: function(json){

                    /*
                    |--------------------------------------------------------------------------
                    | KPIs
                    |--------------------------------------------------------------------------
                    */

                    $('#kpi_total_profit')

                        .html(

                            'S/ ' +

                            parseFloat(

                                json.summary.total_profit

                            ).toFixed(2)

                        );

                    $('#kpi_profitable_cases')

                        .html(

                            json.summary.profitable_cases

                        );

                    $('#kpi_loss_cases')

                        .html(

                            json.summary.loss_cases

                        );

                    $('#kpi_avg_profit')

                        .html(

                            'S/ ' +

                            parseFloat(

                                json.summary.avg_profit

                            ).toFixed(2)

                        );

                    $('#kpi_best_case')

                        .html(

                            json.summary.best_case_title +

                            '<br>' +

                            'S/ ' +

                            parseFloat(

                                json.summary.best_case_profit

                            ).toFixed(2)

                        );

                    $('#kpi_best_client')

                        .html(

                            json.summary.best_client_name +

                            '<br>' +

                            'S/ ' +

                            parseFloat(

                                json.summary.best_client_profit

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
                    data: 'service_type'
                },

                {
                    data: 'status'
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