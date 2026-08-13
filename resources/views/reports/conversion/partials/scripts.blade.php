@push('scripts')

<script>

let conversionCharts = {};

function loadConversionReport() {

    let params = {

        date_from: $('#date_from').val(),

        date_to: $('#date_to').val(),

        establishment_id: $('#establishment_id').val(),

        specialty_id: $('#specialty_id').val(),

        lawyer_id: $('#lawyer_id').val(),

        status: $('#status').val(),

    };

    $.ajax({

        url: "{{ route('reports.conversion.datatable') }}",

        method: 'GET',

        data: params,

        success: function (response) {

            updateKPIs(response.summary);

            renderCharts(response.charts);

            renderTable(response.data);

        },

        error: function (xhr) {

            console.error(xhr.responseText);

        }

    });

}

function updateKPIs(summary) {

    $('#kpi-total-consultations').text(summary.total_consultations);

    $('#kpi-conversion-rate').text(summary.conversion_rate + '%');

    $('#kpi-converted').text(summary.converted);

    $('#kpi-total-follow-ups').text(summary.total_follow_ups);

    $('#kpi-accepted').text(summary.accepted);

    $('#kpi-rejected').text(summary.rejected);

    $('#kpi-in-process').text(summary.in_process);

    $('#kpi-avg-follow-ups').text(summary.avg_follow_ups);

    $('#kpi-avg-conversion-days').text(summary.avg_conversion_days);

}

function renderCharts(charts) {

    // Embudo

    if (conversionCharts.funnel) conversionCharts.funnel.destroy();

    conversionCharts.funnel = new ApexCharts(

        document.querySelector('#chart-funnel'),

        {

            chart: { type: 'bar', height: 300 },

            series: [{

                name: 'Consultas',

                data: charts.funnel.values

            }],

            xaxis: {

                categories: charts.funnel.labels.map(function (s) {

                    return @json(config('options.consultation_statuses'))[s] || s;

                })

            }

        }

    ).render();

    // Evolución mensual

    if (conversionCharts.monthly) conversionCharts.monthly.destroy();

    conversionCharts.monthly = new ApexCharts(

        document.querySelector('#chart-monthly'),

        {

            chart: { type: 'line', height: 300 },

            series: [

                {

                    name: 'Consultas',

                    data: charts.monthly.consultations

                },

                {

                    name: 'Convertidas',

                    data: charts.monthly.converted

                }

            ],

            xaxis: {

                categories: charts.monthly.labels

            }

        }

    ).render();

    // Por especialidad

    if (conversionCharts.specialties) conversionCharts.specialties.destroy();

    conversionCharts.specialties = new ApexCharts(

        document.querySelector('#chart-specialties'),

        {

            chart: { type: 'bar', height: 300 },

            series: [

                {

                    name: 'Convertidas',

                    data: charts.specialties.converted

                },

                {

                    name: 'Totales',

                    data: charts.specialties.total

                }

            ],

            xaxis: {

                categories: charts.specialties.labels

            }

        }

    ).render();

    // Por abogado

    if (conversionCharts.lawyers) conversionCharts.lawyers.destroy();

    conversionCharts.lawyers = new ApexCharts(

        document.querySelector('#chart-lawyers'),

        {

            chart: { type: 'bar', height: 300 },

            series: [

                {

                    name: 'Convertidas',

                    data: charts.lawyers.converted

                },

                {

                    name: 'Totales',

                    data: charts.lawyers.total

                }

            ],

            xaxis: {

                categories: charts.lawyers.labels

            }

        }

    ).render();

    // Resultados de seguimiento

    if (conversionCharts.results) conversionCharts.results.destroy();

    conversionCharts.results = new ApexCharts(

        document.querySelector('#chart-results'),

        {

            chart: { type: 'donut', height: 300 },

            labels: charts.results.labels.map(function (r) {

                return @json(config('options.follow_up_results'))[r] || r;

            }),

            series: charts.results.values

        }

    ).render();

    // Canales de comunicación

    if (conversionCharts.channels) conversionCharts.channels.destroy();

    conversionCharts.channels = new ApexCharts(

        document.querySelector('#chart-channels'),

        {

            chart: { type: 'donut', height: 300 },

            labels: charts.channels.labels.map(function (c) {

                return @json(config('options.communication_types'))[c] || c;

            }),

            series: charts.channels.values

        }

    ).render();

}

function renderTable(data) {

    if ($.fn.DataTable.isDataTable('#conversion-table')) {

        $('#conversion-table').DataTable().clear().destroy();

    }

    $('#conversion-table').DataTable({

        data: data,

        columns: [

            { data: 'title' },

            { data: 'client' },

            { data: 'lawyer' },

            { data: 'specialty' },

            { data: 'status' },

            { data: 'created_at' },

            { data: 'follow_ups' },

            { data: 'last_result' },

            { data: 'next_contact' },

            { data: 'converted' },

            { data: 'converted_at' },

        ],

        order: [[5, 'desc']],

        language: {

            url: '//cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json'

        }

    });

}

$(document).ready(function () {

    $('#btn-search').on('click', function () {

        loadConversionReport();

    });

    loadConversionReport();

});

</script>

@endpush