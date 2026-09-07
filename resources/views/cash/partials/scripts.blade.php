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

    // ==========================================
    // MODAL INGRESO
    // ==========================================

    $('#modalIncome').on('show.bs.modal', function(){
        $('#formIncome')[0].reset();
        $('#income_date').val(formatDate(new Date()));
    });

    $('#formIncome').on('submit', function(e){

        e.preventDefault();

        const $btn = $(this).find('button[type="submit"]');

        $btn.prop('disabled', true);

        $.ajax({
            url: "{{ route('cash.income.store') }}",
            method: 'POST',
            data: $(this).serialize(),
            success: function(){
                $('#modalIncome').modal('hide');
                loadReport();
                showToast('Ingreso registrado correctamente.', 'success');
            },
            error: function(xhr){
                console.error(xhr.responseText);
                showToast('Ocurrió un error al registrar el ingreso.', 'danger');
            },
            complete: function(){
                $btn.prop('disabled', false);
            }
        });
    });

    // ==========================================
    // MODAL GASTO
    // ==========================================

    $('#modalCashExpense').on('show.bs.modal', function(){
        $('#formCashExpense')[0].reset();
        $('#cash_expense_date').val(formatDate(new Date()));
    });

    $('#formCashExpense').on('submit', function(e){

        e.preventDefault();

        const $btn = $(this).find('button[type="submit"]');

        $btn.prop('disabled', true);

        const formData = new FormData(this);

        $.ajax({
            url: "{{ route('expenses.store') }}",
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(){
                $('#modalCashExpense').modal('hide');
                loadReport();
                showToast('Gasto registrado correctamente.', 'success');
            },
            error: function(xhr){
                console.error(xhr.responseText);
                showToast('Ocurrió un error al registrar el gasto.', 'danger');
            },
            complete: function(){
                $btn.prop('disabled', false);
            }
        });
    });

    // ==========================================
    // ELIMINAR MOVIMIENTO
    // ==========================================

    $(document).on('click', '.btn-delete-cash', function(){

        const id = $(this).data('id');
        const kind = $(this).data('kind');

        const message = kind === 'income'
            ? '¿Eliminar este ingreso?'
            : '¿Eliminar este gasto?';

        if(!confirm(message)){
            return;
        }

        const data = { _token: '{{ csrf_token() }}' };
        let url;

        if(kind === 'income'){
            url = "{{ route('cash.income.delete') }}";
            data.id = id;
        } else {
            url = '/expenses/' + id;
            data._method = 'DELETE';
        }

        $.ajax({
            url: url,
            method: 'POST',
            data: data,
            success: function(){
                loadReport();
                showToast('Movimiento eliminado.', 'success');
            },
            error: function(xhr){
                console.error(xhr.responseText);
                showToast('Ocurrió un error al eliminar.', 'danger');
            }
        });
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
            { extend: 'excelHtml5', title: 'Caja Chica' },
            { extend: 'print', title: 'Caja Chica' }
        ],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json'
        },
        columns: [
            { data: 'date' },
            {
                data: 'type',
                render: function(data){
                    if(data === 'Ingreso'){
                        return '<span class="badge bg-success">Ingreso</span>';
                    }
                    return '<span class="badge bg-danger">Gasto</span>';
                }
            },
            { data: 'origin' },
            {
                data: 'user',
                render: function(data){
                    if(!data){
                        return '<span class="text-muted">—</span>';
                    }
                    return escapeHtml(data);
                }
            },
            { data: 'payment_method' },
            {
                data: 'concept',
                render: function(data, type, row){
                    let html = escapeHtml(data);
                    if(!html){
                        html = '<span class="text-muted">—</span>';
                    }
                    if(row.reference){
                        html += '<div class="small text-muted">Ref.: ' + escapeHtml(row.reference) + '</div>';
                    }
                    return html;
                }
            },
            {
                data: 'amount',
                className: 'text-end',
                render: function(data, type, row){
                    const color = row.kind === 'income' ? 'text-success' : 'text-danger';
                    return '<span class="fw-semibold ' + color + '">' + data + '</span>';
                }
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                className: 'text-end',
                render: function(data, type, row){
                    if(!row.deletable){
                        return '<span class="text-muted small">—</span>';
                    }
                    return '<button type="button" class="btn btn-sm btn-outline-danger btn-delete-cash" data-id="' + row.id + '" data-kind="' + row.kind + '"><i class="bi bi-trash"></i> Eliminar</button>';
                }
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
        url: "{{ route('cash.datatable') }}",
        type: "GET",
        data: {
            date_start: $('#date_start').val(),
            date_end: $('#date_end').val(),
            establishment_id: $('#establishment_id').val()
        },
        success: function(response){
            loadKpis(response.kpis);

            if(response.methods){
                loadMethodsTable(response.methods);
            }

            if(response.chart){
                loadChart(response.chart);
            }

            loadMovements(response.rows);
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
    $('#kpi-income').html('S/ ' + formatMoney(kpis.income));
    $('#kpi-expense').html('S/ ' + formatMoney(kpis.expense));
    $('#kpi-net').html('S/ ' + formatMoney(kpis.net));
    $('#kpi-count').html(kpis.movements_count);
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
                <td>${row.method}</td>
                <td class="text-success text-end">${formatMoney(row.income)}</td>
                <td class="text-danger text-end">${formatMoney(row.expense)}</td>
                <td class="fw-bold text-end">${formatMoney(row.net)}</td>
            </tr>
        `;
    });

    $('#methodsTable tbody').html(html);
}

// =====================================================
// CHART
// =====================================================

function loadChart(chart)
{
    if(cashChart){
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
            categories: chart.categories
        },
        dataLabels: {
            enabled: false
        },
        legend: {
            position: 'top'
        }
    };

    cashChart = new ApexCharts(
        document.querySelector('#cashChart'),
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

    if(range === 'today'){
        from = formatDate(today);
        to = formatDate(today);
    }

    if(range === 'yesterday'){
        let yesterday = new Date();
        yesterday.setDate(yesterday.getDate() - 1);
        from = formatDate(yesterday);
        to = formatDate(yesterday);
    }

    if(range === 'week'){
        let firstDay = new Date(today);
        firstDay.setDate(today.getDate() - today.getDay());
        from = formatDate(firstDay);
        to = formatDate(today);
    }

    if(range === 'month'){
        let firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
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

function formatMoney(value)
{
    return Number(value).toLocaleString('es-PE', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

function escapeHtml(value)
{
    if(value == null){
        return '';
    }
    return String(value).replace(/[&<>"']/g, function(m){
        return {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        }[m];
    });
}

function showToast(message, type)
{
    const $toast = $('#cashToast');

    $toast
        .removeClass('text-bg-success text-bg-danger')
        .addClass(type === 'danger' ? 'text-bg-danger' : 'text-bg-success');

    $('#cashToastMessage').text(message);

    bootstrap.Toast.getOrCreateInstance($toast[0]).show();
}

</script>

@endpush


