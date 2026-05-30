@push('scripts')

<script>

let table;

let chartStatus;
let chartLawyers;
let chartEstablishments;
let chartMonthly;
let chartAging;

function destroyCharts()
{
    if(chartStatus) chartStatus.destroy();
    if(chartLawyers) chartLawyers.destroy();
    if(chartEstablishments) chartEstablishments.destroy();
    if(chartMonthly) chartMonthly.destroy();
    if(chartAging) chartAging.destroy();
}

function loadCharts(charts)
{
    destroyCharts();

    /*
    |--------------------------------------------------------------------------
    | Estado cuotas
    |--------------------------------------------------------------------------
    */

    chartStatus = new ApexCharts(

        document.querySelector("#chart_status"),

        {
            chart: {
                type: 'donut',
                height: 320
            },

            series: [

                charts.status.paid,
                charts.status.pending,
                charts.status.overdue

            ],

            labels: [

                'Pagadas',
                'Pendientes',
                'Vencidas'

            ],

            legend: {
                position: 'bottom'
            }
        }

    );

    chartStatus.render();

    /*
    |--------------------------------------------------------------------------
    | Cobranza abogado
    |--------------------------------------------------------------------------
    */

    chartLawyers = new ApexCharts(

        document.querySelector("#chart_lawyers"),

        {

            chart: {
                type: 'bar',
                height: 350
            },

            series: [{

                name: 'Cobrado',

                data: charts.lawyers.map(
                    x => parseFloat(x.total)
                )

            }],

            xaxis: {

                categories:
                    charts.lawyers.map(
                        x => x.lawyer
                    )

            }

        }

    );

    chartLawyers.render();

    /*
    |--------------------------------------------------------------------------
    | Cobranza sede
    |--------------------------------------------------------------------------
    */

    chartEstablishments = new ApexCharts(

        document.querySelector("#chart_establishments"),

        {

            chart: {
                type: 'bar',
                height: 350
            },

            series: [{

                name: 'Cobrado',

                data: charts.establishments.map(
                    x => parseFloat(x.total)
                )

            }],

            xaxis: {

                categories:
                    charts.establishments.map(
                        x => x.establishment
                    )

            }

        }

    );

    chartEstablishments.render();

    /*
    |--------------------------------------------------------------------------
    | Evolución mensual
    |--------------------------------------------------------------------------
    */

    chartMonthly = new ApexCharts(

        document.querySelector("#chart_monthly"),

        {

            chart: {
                type: 'line',
                height: 350
            },

            stroke: {
                curve: 'smooth'
            },

            series: [{

                name: 'Cobrado',

                data: charts.monthly.map(
                    x => parseFloat(x.total)
                )

            }],

            xaxis: {

                categories:
                    charts.monthly.map(
                        x => x.month
                    )

            }

        }

    );

    chartMonthly.render();

    /*
    |--------------------------------------------------------------------------
    | Antigüedad deuda
    |--------------------------------------------------------------------------
    */

    chartAging = new ApexCharts(

        document.querySelector("#chart_aging"),

        {

            chart: {
                type: 'bar',
                height: 320
            },

            series: [{

                name: 'Deuda',

                data: [

                    charts.aging['1_7'],
                    charts.aging['8_30'],
                    charts.aging['31_60'],
                    charts.aging['60_plus']

                ]

            }],

            xaxis: {

                categories: [

                    '1-7 días',
                    '8-30 días',
                    '31-60 días',
                    '+60 días'

                ]

            }

        }

    );

    chartAging.render();
}

function loadTopClients(topClients)
{
    let html = '';

    topClients.forEach(item => {

        html += `
            <tr>
                <td>${item.client}</td>
                <td>S/ ${parseFloat(item.debt).toFixed(2)}</td>
            </tr>
        `;

    });

    $('#table-top-clients tbody')
        .html(html);
}

function loadLawyerRanking(ranking)
{
    let html = '';

    ranking.forEach(item => {

        html += `
            <tr>
                <td>${item.lawyer}</td>
                <td>S/ ${parseFloat(item.collected).toFixed(2)}</td>
                <td>S/ ${parseFloat(item.pending).toFixed(2)}</td>
            </tr>
        `;

    });

    $('#table-ranking-lawyers tbody')
        .html(html);
}

function loadTable()
{
    if(table){

        table.destroy();

    }

    table = $('#table-collection').DataTable({

        processing: true,

        responsive: true,

        destroy: true,

        pageLength: 25,

        dom: 'Bfrtip',

        buttons: [

            {

                extend: 'excelHtml5',

                title: 'Reporte de Cobranza',

                sheetName: 'Hoja1',

                filename: function(){

                    let now = new Date();

                    return 'Reporte_Cobranza_' +

                        now.getFullYear() +

                        '-' +

                        String(now.getMonth()+1)
                            .padStart(2,'0') +

                        '-' +

                        String(now.getDate())
                            .padStart(2,'0') +

                        '_' +

                        String(now.getHours())
                            .padStart(2,'0') +

                        '-' +

                        String(now.getMinutes())
                            .padStart(2,'0');

                }

            },

            {

                extend: 'print',

                title: 'Reporte de Cobranza'

            }

        ],

        language: {

            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'

        },

        ajax: {

            url: "{{ route('reports.collection.datatable') }}",

            data: function(d){

                d.date_from =
                    $('#date_from').val();

                d.date_to =
                    $('#date_to').val();

                d.establishment_id =
                    $('#establishment_id').val();

                d.lawyer_id =
                    $('#lawyer_id').val();

                d.status =
                    $('#status').val();

                d.include_overdue =
                    $('#include_overdue')
                        .is(':checked')
                            ? 1
                            : 0;

            },

            dataSrc: function(json){

                /*
                |--------------------------------------------------------------------------
                | KPIs
                |--------------------------------------------------------------------------
                */

                $('#kpi_collected')
                    .html(
                        'S/ ' +
                        json.summary.collected
                    );

                $('#kpi_pending')
                    .html(
                        'S/ ' +
                        json.summary.pending
                    );

                $('#kpi_overdue')
                    .html(
                        'S/ ' +
                        json.summary.overdue
                    );

                $('#kpi_installments')
                    .html(
                        json.summary.installments
                    );

                $('#kpi_morose')
                    .html(
                        json.summary.morose_clients
                    );

                $('#kpi_effectiveness')
                    .html(
                        json.summary.effectiveness
                        + '%'
                    );

                $('#kpi_avg_days_late')
                    .html(
                        json.summary.avg_days_late
                        + ' días'
                    );

                /*
                |--------------------------------------------------------------------------
                | Charts
                |--------------------------------------------------------------------------
                */

                loadCharts(
                    json.charts
                );

                /*
                |--------------------------------------------------------------------------
                | Top clientes
                |--------------------------------------------------------------------------
                */

                loadTopClients(
                    json.top_clients
                );

                /*
                |--------------------------------------------------------------------------
                | Ranking abogados
                |--------------------------------------------------------------------------
                */

                loadLawyerRanking(
                    json.lawyer_ranking
                );

                return json.data;

            }

        },

        columns: [

            {data:'client'},
            {data:'consultation'},
            {data:'lawyer'},
            {data:'establishment'},
            {data:'installment'},
            {data:'due_date'},
            {data:'days_late'},
            {data:'amount'},
            {data:'paid'},
            {data:'pending'},
            {data:'status'}

        ],

        columnDefs: [

            {
                targets: [10],
                orderable: false
            }

        ],

        rowCallback: function(row, data){

            if(
                data.status_raw
                ==
                'overdue'
            ){

                $(row)
                    .addClass(
                        'table-danger'
                    );

            }

        }

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