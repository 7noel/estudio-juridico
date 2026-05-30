@push('scripts')

<script>

let financialTable;

let chartIncomeExpense;
let chartExpenseCategory;
let chartEstablishments;
let chartSpecialties;
let chartProfitMonthly;

function destroyCharts()
{
    if(chartIncomeExpense) chartIncomeExpense.destroy();
    if(chartExpenseCategory) chartExpenseCategory.destroy();
    if(chartEstablishments) chartEstablishments.destroy();
    if(chartSpecialties) chartSpecialties.destroy();
    if(chartProfitMonthly) chartProfitMonthly.destroy();
}

function loadCharts(charts)
{
    destroyCharts();

    /*
    |--------------------------------------------------------------------------
    | Ingresos vs Gastos
    |--------------------------------------------------------------------------
    */

    chartIncomeExpense = new ApexCharts(

        document.querySelector('#chart_income_expense'),

        {

            chart: {

                type: 'line',

                height: 350

            },

            stroke: {

                curve: 'smooth'

            },

            series: [

                {

                    name: 'Ingresos',

                    data: charts.income_expense.income

                },

                {

                    name: 'Gastos',

                    data: charts.income_expense.expense

                }

            ],

            xaxis: {

                categories:
                    charts.income_expense.labels

            }

        }

    );

    chartIncomeExpense.render();

    /*
    |--------------------------------------------------------------------------
    | Gastos por categoría
    |--------------------------------------------------------------------------
    */

    chartExpenseCategory = new ApexCharts(

        document.querySelector('#chart_expense_category'),

        {

            chart: {

                type: 'donut',

                height: 350

            },

            series:
                charts.expense_category.values,

            labels:
                charts.expense_category.labels

        }

    );

    chartExpenseCategory.render();

    /*
    |--------------------------------------------------------------------------
    | Ingresos por sede
    |--------------------------------------------------------------------------
    */

    chartEstablishments = new ApexCharts(

        document.querySelector('#chart_establishments'),

        {

            chart: {

                type: 'bar',

                height: 350

            },

            series: [

                {

                    name: 'Ingresos',

                    data:
                        charts.establishments.values

                }

            ],

            xaxis: {

                categories:
                    charts.establishments.labels

            }

        }

    );

    chartEstablishments.render();

    /*
    |--------------------------------------------------------------------------
    | Ingresos por especialidad
    |--------------------------------------------------------------------------
    */

    chartSpecialties = new ApexCharts(

        document.querySelector('#chart_specialties'),

        {

            chart: {

                type: 'bar',

                height: 350

            },

            series: [

                {

                    name: 'Ingresos',

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
    | Utilidad mensual
    |--------------------------------------------------------------------------
    */

    chartProfitMonthly = new ApexCharts(

        document.querySelector('#chart_profit_monthly'),

        {

            chart: {

                type: 'bar',

                height: 350

            },

            series: [

                {

                    name: 'Utilidad',

                    data:
                        charts.profit_monthly.values

                }

            ],

            xaxis: {

                categories:
                    charts.profit_monthly.labels

            }

        }

    );

    chartProfitMonthly.render();
}

/*
|--------------------------------------------------------------------------
| Resumen por sede
|--------------------------------------------------------------------------
*/

function loadEstablishmentSummary(rows)
{
    let html = '';

    rows.forEach(item => {

        html += `

            <tr>

                <td>${item.establishment}</td>

                <td>S/ ${parseFloat(item.income).toFixed(2)}</td>

                <td>S/ ${parseFloat(item.expense).toFixed(2)}</td>

                <td>S/ ${parseFloat(item.profit).toFixed(2)}</td>

                <td>${item.margin}%</td>

            </tr>

        `;

    });

    $('#table-establishments tbody')
        .html(html);
}

/*
|--------------------------------------------------------------------------
| Tabla principal
|--------------------------------------------------------------------------
*/

function loadTable()
{
    if(financialTable){

        financialTable.destroy();

    }

    financialTable = $('#table-financial').DataTable({

        processing: true,

        destroy: true,

        responsive: true,

        pageLength: 25,

        dom: 'Bfrtip',

        buttons: [

            {

                extend: 'excelHtml5',

                title: 'Reporte Financiero',

                sheetName: 'Hoja1',

                filename: function(){

                    let now = new Date();

                    return 'Reporte_Financiero_' +

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

                title: 'Reporte Financiero'

            }

        ],

        language: {

            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'

        },

        ajax: {

            url:
                "{{ route('reports.financial.datatable') }}",

            data: function(d){

                d.date_from =
                    $('#date_from').val();

                d.date_to =
                    $('#date_to').val();

                d.establishment_id =
                    $('#establishment_id').val();

                d.specialty_id =
                    $('#specialty_id').val();

                d.service_type =
                    $('#service_type').val();

            },

            dataSrc: function(json){

                /*
                |--------------------------------------------------------------------------
                | KPIs
                |--------------------------------------------------------------------------
                */

                $('#kpi_income')
                    .html(
                        'S/ ' +
                        parseFloat(
                            json.summary.income
                        ).toFixed(2)
                    );

                $('#kpi_expense')
                    .html(
                        'S/ ' +
                        parseFloat(
                            json.summary.expense
                        ).toFixed(2)
                    );

                $('#kpi_profit')
                    .html(
                        'S/ ' +
                        parseFloat(
                            json.summary.profit
                        ).toFixed(2)
                    );

                $('#kpi_net_flow')
                    .html(
                        'S/ ' +
                        parseFloat(
                            json.summary.net_flow
                        ).toFixed(2)
                    );

                $('#kpi_margin')
                    .html(
                        json.summary.margin +
                        '%'
                    );

                $('#kpi_ticket')
                    .html(
                        'S/ ' +
                        parseFloat(
                            json.summary.avg_ticket
                        ).toFixed(2)
                    );

                $('#kpi_roi')
                    .html(
                        json.summary.roi +
                        'x'
                    );

                /*
                |--------------------------------------------------------------------------
                | Gráficos
                |--------------------------------------------------------------------------
                */

                if(json.charts){

                    loadCharts(
                        json.charts
                    );

                }

                /*
                |--------------------------------------------------------------------------
                | Resumen sedes
                |--------------------------------------------------------------------------
                */

                if(
                    json.establishment_summary
                ){

                    loadEstablishmentSummary(

                        json.establishment_summary

                    );

                }

                return json.data;

            }

        },

        columns: [

            {data:'date'},

            {data:'type'},

            {data:'concept'},

            {data:'establishment'},

            {data:'client'},

            {data:'amount'}

        ]

    });

}

loadTable();

$('#btn-search').on(

    'click',

    function(){

        loadTable();

    }

);

</script>

@endpush