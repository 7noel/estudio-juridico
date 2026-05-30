@push('scripts')

<script>

let cashTable;
let cashChart = null;

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

        let range = $(this).data('range');

        applyQuickRange(range);

        loadReport();

    });

});

// =====================================================
// DATATABLE
// =====================================================

function initDataTable()
{
    cashTable = $('#cashTable').DataTable({

        processing: true,

        searching: true,

        paging: true,

        ordering: true,

        destroy: true,

        dom: 'Bfrtip',

        buttons: [

            {
                extend: 'excelHtml5',
                title: 'Reporte de Caja'
            },

            {
                extend: 'print',
                title: 'Reporte de Caja'
            }

        ],

        language: {

            url: '//cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json'

        },

        columns: [

            {
                data: 'date'
            },

            {
                data: 'type',
                render: function(data){

                    if(data === 'Ingreso')
                    {
                        return `
                            <span class="badge bg-success">
                                Ingreso
                            </span>
                        `;
                    }

                    return `
                        <span class="badge bg-danger">
                            Gasto
                        </span>
                    `;
                }
            },

            {
                data: 'payment_method'
            },

            {
                data: 'concept'
            },

            {
                data: 'amount',
                className: 'text-end'
            }

        ]

    });
}

// =====================================================
// LOAD REPORT
// =====================================================

function loadReport()
{
    $.ajax({

        url: "{{ route('reports.cash.datatable') }}",

        type: "GET",

        data: {

            date_start:
                $('#date_start').val(),

            date_end:
                $('#date_end').val(),

            establishment_id:
                $('#establishment_id').val(),

        },

        success: function(response){

            loadKpis(
                response.kpis
            );

            loadMethodsTable(
                response.methods
            );

            loadChart(
                response.chart
            );

            loadMovements(
                response.rows
            );

        },

        error: function(xhr){

            console.error(xhr);

        }

    });
}

// =====================================================
// KPIS
// =====================================================

function loadKpis(kpis)
{
    $('#kpi-income').html(
        'S/ ' + formatMoney(kpis.income)
    );

    $('#kpi-expense').html(
        'S/ ' + formatMoney(kpis.expense)
    );

    $('#kpi-net').html(
        'S/ ' + formatMoney(kpis.net)
    );

    $('#kpi-count').html(
        kpis.payments_count
    );
}

// =====================================================
// METHODS TABLE
// =====================================================

function loadMethodsTable(methods)
{
    let html = '';

    methods.forEach(function(row){

        html += `
            <tr>

                <td>
                    ${row.method}
                </td>

                <td class="text-success text-end">
                    ${formatMoney(row.income)}
                </td>

                <td class="text-danger text-end">
                    ${formatMoney(row.expense)}
                </td>

                <td class="fw-bold text-end">
                    ${formatMoney(row.net)}
                </td>

            </tr>
        `;

    });

    $('#methodsTable tbody')
        .html(html);
}

// =====================================================
// CHART
// =====================================================

function loadChart(chart)
{
    if(cashChart)
    {
        cashChart.destroy();
    }

    let options = {

        chart: {

            type: 'bar',

            height: 350

        },

        series: [

            {

                name: 'Ingresos',

                data: chart.income

            },

            {

                name: 'Gastos',

                data: chart.expense

            }

        ],

        xaxis: {

            categories:
                chart.categories

        },

        dataLabels: {

            enabled: false

        },

        legend: {

            position: 'top'

        }

    };

    cashChart = new ApexCharts(

        document.querySelector(
            '#cashChart'
        ),

        options

    );

    cashChart.render();
}

// =====================================================
// MOVEMENTS
// =====================================================

function loadMovements(rows)
{
    cashTable.clear();

    cashTable.rows.add(rows);

    cashTable.draw();
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

        yesterday.setDate(
            yesterday.getDate() - 1
        );

        from = formatDate(yesterday);
        to = formatDate(yesterday);
    }

    if(range === 'week')
    {
        let firstDay = new Date(today);

        firstDay.setDate(
            today.getDate() -
            today.getDay()
        );

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
    let month =
        String(date.getMonth() + 1)
            .padStart(2, '0');

    let day =
        String(date.getDate())
            .padStart(2, '0');

    return `${date.getFullYear()}-${month}-${day}`;
}

function formatMoney(value)
{
    return Number(value)
        .toLocaleString(
            'es-PE',
            {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }
        );
}

</script>

@endpush