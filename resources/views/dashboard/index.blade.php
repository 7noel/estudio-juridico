@extends('layouts.app')

@section('content')

<div class="container-fluid">

    {{-- ========================================= --}}
    {{-- CARDS --}}
    {{-- ========================================= --}}

    <div class="row g-3 mb-4">

        <div class="col-md-3">

            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div>

                            <div class="text-muted small">
                                Consultas pendientes
                            </div>

                            <h2 class="mb-0 mt-2">
                                {{ $pendingConsultations }}
                            </h2>

                        </div>

                        <div class="fs-1 text-primary">
                            <i class="bi bi-chat-left-text"></i>
                        </div>

                    </div>

                </div>
            </div>

        </div>

        <div class="col-md-3">

            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div>

                            <div class="text-muted small">
                                Casos en proceso
                            </div>

                            <h2 class="mb-0 mt-2">
                                {{ $casesInProgress }}
                            </h2>

                        </div>

                        <div class="fs-1 text-success">
                            <i class="bi bi-folder2-open"></i>
                        </div>

                    </div>

                </div>
            </div>

        </div>

        <div class="col-md-3">

            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div>

                            <div class="text-muted small">
                                Eventos hoy
                            </div>

                            <h2 class="mb-0 mt-2">
                                {{ $todayEvents }}
                            </h2>

                        </div>

                        <div class="fs-1 text-warning">
                            <i class="bi bi-calendar-event"></i>
                        </div>

                    </div>

                </div>
            </div>

        </div>

        <div class="col-md-3">

            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div>

                            <div class="text-muted small">
                                Pagos pendientes
                            </div>

                            <h2 class="mb-0 mt-2">
                                S/ {{ number_format($pendingPayments, 2) }}
                            </h2>

                        </div>

                        <div class="fs-1 text-danger">
                            <i class="bi bi-cash-stack"></i>
                        </div>

                    </div>

                </div>
            </div>

        </div>

    </div>

    {{-- ========================================= --}}
    {{-- CALENDARIO + ACTIVIDAD --}}
    {{-- ========================================= --}}

    <div class="row g-3 mb-4">

        {{-- CALENDARIO --}}
        <div class="col-lg-7">

            <div class="card shadow-sm border-0">

                <div class="card-header bg-white">

                    <strong>
                        Calendario
                    </strong>

                </div>

                <div class="card-body p-3">

                    <div id="dashboardCalendar"></div>

                </div>

            </div>

        </div>

        {{-- ACTIVIDAD --}}
        <div class="col-lg-5">

            <div class="card shadow-sm border-0">

                <div class="card-header bg-white">

                    <strong>
                        Actividad reciente
                    </strong>

                </div>

                <div class="card-body p-0">

                    <div class="list-group list-group-flush">

                        @forelse($recentActivities as $activity)

                            <div class="list-group-item">

                                <div class="d-flex justify-content-between">

                                    <div>

                                        <div class="fw-semibold">
                                            {{ $activity->title }}
                                        </div>

                                        <div class="small text-muted">

                                            Caso #{{ $activity->case->id ?? '-' }}

                                        </div>

                                    </div>

                                    <small class="text-muted">

                                        {{ $activity->created_at->diffForHumans() }}

                                    </small>

                                </div>

                                <div class="mt-2 small">

                                    {{ Str::limit($activity->description, 120) }}

                                </div>

                            </div>

                        @empty

                            <div class="p-3 text-muted">

                                No hay actividad reciente

                            </div>

                        @endforelse

                    </div>

                </div>

            </div>

        </div>

    </div>

    {{-- ========================================= --}}
    {{-- GRAFICOS --}}
    {{-- ========================================= --}}

    <div class="row g-3">

        {{-- CASOS POR ESTADO --}}
        <div class="col-lg-6">

            <div class="card shadow-sm border-0">

                <div class="card-header bg-white">

                    <strong>
                        Casos por estado
                    </strong>

                </div>

                <div class="card-body">

                    <canvas id="chartCasesStatus"></canvas>

                </div>

            </div>

        </div>

        {{-- CASOS POR ESPECIALIDAD --}}
        <div class="col-lg-6">

            <div class="card shadow-sm border-0">

                <div class="card-header bg-white">

                    <strong>
                        Casos por especialidad
                    </strong>

                </div>

                <div class="card-body">

                    <canvas id="chartCasesSpecialty"></canvas>

                </div>

            </div>

        </div>

    </div>

</div>


<div class="modal fade" id="calendarEventModal">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    Evento
                </h5>

                <button class="btn-close"
                        data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body">

                <h5 id="modal_event_title"></h5>

                <div class="mb-2 text-muted"
                     id="modal_event_dates">
                </div>

                <div class="mb-3"
                     id="modal_event_description">
                </div>

                <div>
                    <strong>Ubicación:</strong>
                    <span id="modal_event_location"></span>
                </div>

            </div>

        </div>

    </div>

</div>

@endsection

@push('styles')

<style>
.fc {

    font-size: 14px;

}

.fc .fc-toolbar-title {

    font-size: 1.3rem;
    font-weight: 600;

}

.fc .fc-button {

    background: #ffffff !important;
    border: 1px solid #dbe2ea !important;
    color: #334155 !important;

    border-radius: 10px !important;
    padding: 6px 14px !important;

    font-weight: 500;
    box-shadow: 0 1px 2px rgba(0,0,0,.04);

    transition: all .2s ease;

}

.fc .fc-button:hover {

    background: #f1f5f9 !important;
    border-color: #cbd5e1 !important;
    color: #0f172a !important;

}

.fc .fc-button.fc-button-active {

    background: #2563eb !important;
    border-color: #2563eb !important;
    color: #fff !important;

}

.fc .fc-button:focus {

    box-shadow: 0 0 0 .2rem rgba(37,99,235,.15) !important;

}

.fc .fc-daygrid-day {

    transition: background-color .2s;

}

.fc .fc-daygrid-day:hover {

    background: #f8f9fa;

}

.fc .fc-event {

    background: #3b82f6;
    border: none !important;

    color: white;

    border-radius: 8px !important;

    padding: 3px 7px;

    font-size: 12px;
    font-weight: 500;

    box-shadow: 0 1px 2px rgba(0,0,0,.10);

}

.fc .fc-event:hover {

    transform: translateY(-1px);

    transition: .15s;

}

.fc-event-title {

    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;

}

.fc .fc-day-today {

    background: #eff6ff !important;
    position: relative;

}

.fc .fc-day-today::after {

    content: '';
    position: absolute;

    top: 0;
    left: 0;

    width: 4px;
    height: 100%;

    background: #2563eb;

}

.fc-theme-standard td,
.fc-theme-standard th {

    border-color: #ebeef2;

}

.fc-scrollgrid {

    border-radius: 12px;
    overflow: hidden;

}

</style>

@endpush

@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    // =====================================================
    // CALENDARIO
    // =====================================================

    const calendarEl =
        document.getElementById('dashboardCalendar');

    const calendar = new FullCalendar.Calendar(calendarEl, {

        initialView: 'dayGridMonth',

        locale: 'es',

        expandRows: true,
        stickyHeaderDates: true,

        headerToolbar: {

            left: 'prev,next today',

            center: 'title',

            right: ''

        },

        events: @json($calendarEvents),

        eventTimeFormat: {

            hour: '2-digit',

            minute: '2-digit',

            hour12: true

        },
        buttonText: {

            today: 'Hoy',
            month: 'Mes',
            week: 'Semana',
            day: 'día',

        },
        headerToolbar: {

            left: 'prev,next today',

            center: 'title',

            right: 'dayGridMonth,timeGridWeek,timeGridDay'

        },
        slotMinTime: '07:00:00',
        slotMaxTime: '22:00:00',

        eventDidMount: function(info){

            let description =
                info.event.extendedProps.description ?? '';

            let location =
                info.event.extendedProps.location ?? '';

            let start =
                info.event.start
                    ? info.event.start.toLocaleString()
                    : '';

            let end =
                info.event.end
                    ? info.event.end.toLocaleString()
                    : '';

            let html = `
                <div class="text-start">

                    <strong>
                        ${info.event.title}
                    </strong>

                    <hr class="my-1">

                    <div>
                        ${description}
                    </div>

                    <div class="mt-1">
                        📍 ${location}
                    </div>

                    <div class="mt-1">
                        🕒 ${start}
                    </div>

                </div>
            `;

            $(info.el).tooltip({

                title: html,

                html: true,

                placement: 'top',

                trigger: 'hover',

                container: 'body'

            });

        },

        eventClick: function(info){

            $('#modal_event_title').text(
                info.event.title
            );

            $('#modal_event_description').text(
                info.event.extendedProps.description ?? ''
            );

            $('#modal_event_location').text(
                info.event.extendedProps.location ?? '-'
            );

            let start =
                info.event.start
                    ? info.event.start.toLocaleString()
                    : '';

            let end =
                info.event.end
                    ? info.event.end.toLocaleString()
                    : '';

            $('#modal_event_dates').text(
                `${start} - ${end}`
            );

            $('#calendarEventModal').modal('show');

        },

    });

    calendar.render();

    // =====================================================
    // CHART CASOS ESTADO
    // =====================================================

    const ctxStatus =
        document.getElementById('chartCasesStatus');

    new Chart(ctxStatus, {

        type: 'doughnut',

        data: {

            labels: @json(
                collect($casesByStatus)
                    ->keys()
                    ->map(function($status){

                        return config('options.case_statuses')[$status]
                            ?? $status;

                    })
            ),

            datasets: [{

                data: @json(
                    collect($casesByStatus)->values()
                ),

                backgroundColor: [

                    '#0d6efd',
                    '#198754',
                    '#ffc107',
                    '#dc3545',
                    '#6f42c1',
                    '#20c997'

                ]

            }]

        },

        options: {

            responsive: true,

            plugins: {

                legend: {

                    position: 'bottom'

                }

            }

        }

    });

    // =====================================================
    // CHART ESPECIALIDADES
    // =====================================================

    const ctxSpecialty =
        document.getElementById('chartCasesSpecialty');

    new Chart(ctxSpecialty, {

        type: 'bar',

        data: {

            labels: @json(
                collect($casesBySpecialty)->keys()
            ),

            datasets: [{

                label: 'Casos',

                data: @json(
                    collect($casesBySpecialty)->values()
                )

            }]

        },

        options: {

            responsive: true,

            plugins: {

                legend: {

                    display: false

                }

            },

            scales: {

                y: {

                    beginAtZero: true

                }

            }

        }

    });

});

</script>

@endpush