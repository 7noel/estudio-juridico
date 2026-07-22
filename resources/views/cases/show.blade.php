@extends('layouts.app')

@section('content')

@php

    $canManageCaseContent =
        $case->status === 'in_progress';

    $canViewCaseContent =
        $case->status !== 'open';

@endphp

<div class="card">

    {{-- HEADER --}}
    <div class="card-header d-flex justify-content-between align-items-center">

        <h6 class="mb-0">
            Caso #{{ $case->id }}
        </h6>
        <div class="d-flex gap-2">

            @if($case->status == 'in_progress')
            <button class="btn btn-sm btn-outline-secondary left" id="btnEditCase">
                <i class="bi bi-pencil"></i> Editar
            </button>
            @endif

            {{-- BOTONES SEGÚN ESTADO --}}
            @if($case->status == 'open')
                <button class="btn btn-sm btn-primary btn-change-status" data-status="in_progress">
                    <i class="bi bi-play-fill"></i> Iniciar Caso
                </button>
            @endif

            @if($case->status == 'in_progress')
                <button class="btn btn-sm btn-warning btn-change-status" data-status="on_hold">
                    <i class="bi bi-pause-fill"></i> Pausar Caso
                </button>

                <button class="btn btn-sm btn-success btn-change-status" data-status="closed">
                    <i class="bi bi-check-circle-fill"></i> Cerrar Caso
                </button>
            @endif

            @if($case->status == 'on_hold')
                <button class="btn btn-sm btn-primary btn-change-status" data-status="in_progress">
                    <i class="bi bi-play-circle"></i> Reanudar Caso
                </button>

                <button class="btn btn-sm btn-success btn-change-status" data-status="closed">
                    <i class="bi bi-check-circle-fill"></i> Finalizar Caso
                </button>
            @endif

        </div>

    </div>

    {{-- BODY --}}
    <div class="card-body">

        {{-- INFO PRINCIPAL --}}
        <div class="card shadow-sm mb-3">
            <div class="card-header">
                <strong> Información del caso</strong>
            </div>

            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-md-3">
                        <strong>Tipo de servicio:</strong><br>
                        {{ config('options.service_types')[$case->service_type] ?? '-' }}
                    </div>

                    <div class="col-md-3">
                        <strong>Especialidad:</strong><br>
                        {{ $case->specialty->name ?? '-' }}
                    </div>

                    <div class="col-md-3">
                        <strong>Materia:</strong><br>
                        {{ $case->subject->name ?? '-' }}
                    </div>

                    <div class="col-md-3">
                        <strong>Abogado:</strong><br>
                        <span id="case-lawyer">
                            {{ $case->lawyer->name ?? '-' }}
                        </span>
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-md-6">
                        <strong>Cliente:</strong><br>
                        {{ $case->client->full_name ?? '-' }}
                    </div>

                    <div class="col-md-3">
                        <strong>Juzgado:</strong><br>
                        <span id="case-court-name">
                            {{ $case->court_name ?? '-' }}
                        </span>
                    </div>

                    <div class="col-md-3">
                        <strong>Expediente:</strong><br>
                        <span id="case-case-number">
                            {{ $case->case_number ?? '-' }}
                        </span>
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-md-6">
                        <strong>Título:</strong><br>
                        <span id="case-title">
                            {{ $case->title }}
                        </span>
                    </div>

                    <div class="col-md-3">
                        <strong>Estado Procesal:</strong><br>
                        <span class="badge bg-{{ config('options.case_status_colors')[$case->status] }}">
                            {{ config('options.case_statuses')[$case->status] }}
                        </span>
                    </div>

                    <div class="col-md-3">
                        <strong>Estado financiero:</strong><br>
                        @if($case->consultation)
                            <a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#financialStatusModal">
                                <span class="badge bg-{{ $case->consultation->financial_status_color }}">
                                    {{ $case->consultation->financial_status_label }}
                                </span>
                            </a>
                        @else
                            <span class="badge bg-secondary">
                                Sin consulta
                            </span>
                        @endif
                    </div>

                </div>
                <div class="row mb-2">
                    <div class="col-md-12">
                        <strong>Descripción:</strong><br>
                        <span id="case-description">
                            {{ $case->description ?? 'Sin descripción' }}
                        </span>
                    </div>
                </div>

            </div>
        </div>

        @if($canViewCaseContent)

        <ul class="nav nav-tabs mt-4" id="caseTabs" role="tablist">

            <li class="nav-item" role="presentation">

                <button
                    class="nav-link active"
                    id="activities-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#activities-pane"
                    type="button">

                    <i class="bi bi-list-task"></i>

                    Actividades

                    <span class="badge bg-secondary ms-1">

                        {{ $case->activities->count() }}

                    </span>

                </button>

            </li>

            <li class="nav-item" role="presentation">

                <button
                    class="nav-link"
                    id="documents-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#documents-pane"
                    type="button">

                    <i class="bi bi-folder2-open"></i>

                    Documentos

                    <span class="badge bg-secondary ms-1">

                        {{ $case->documents->count() }}

                    </span>

                </button>

            </li>

            <li class="nav-item" role="presentation">

                <button
                    class="nav-link"
                    id="expenses-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#expenses-pane"
                    type="button">

                    <i class="bi bi-cash-stack"></i>

                    Gastos

                    <span class="badge bg-secondary ms-1">

                        {{ $case->expenses->count() }}

                    </span>

                </button>

            </li>

            <li class="nav-item" role="presentation">

                <button
                    class="nav-link"
                    id="agenda-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#agenda-pane"
                    type="button">

                    <i class="bi bi-calendar-event"></i>

                    Agenda

                    <span class="badge bg-secondary ms-1">

                        {{ $case->agendaEvents->count() }}

                    </span>

                </button>

            </li>

        </ul>


        <div class="tab-content mt-3">

            <div
                class="tab-pane fade show active"
                id="activities-pane">

                @include('cases.partials.activities')

            </div>

            <div
                class="tab-pane fade"
                id="documents-pane">

                @include('cases.partials.documents')

            </div>

            <div
                class="tab-pane fade"
                id="expenses-pane">

                @include('cases.partials.expenses')

            </div>

            <div
                class="tab-pane fade"
                id="agenda-pane">

                @include('cases.partials.events')

            </div>

        </div>

        @else

        <div class="alert alert-warning">

            Debes iniciar el caso para registrar
            actividades, documentos, gastos y agenda.

        </div>

        @endif

    </div>

</div>

<div class="modal fade" id="modalEditCase">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    Editar caso
                </h5>

                <button
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body">

                <form id="formEditCase">

                    <div class="mb-3">

                        <label>
                            Juzgado
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="edit_court_name"
                            value="{{ $case->court_name }}">

                    </div>

                    <div class="mb-3">

                        <label>
                            Número expediente
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="edit_case_number"
                            value="{{ $case->case_number }}">

                    </div>

                    <div class="mb-3">

                        <label>
                            Título
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="edit_title"
                            value="{{ $case->title }}">

                    </div>

                    <div class="mb-3">

                        <label>
                            Descripción
                        </label>

                        <textarea
                            class="form-control"
                            id="edit_description"
                            rows="4">{{ $case->description }}</textarea>

                    </div>

                    @if(auth()->user()->hasRole('Administrador'))

                        <div class="mb-3">

                            <label>
                                Abogado
                            </label>

                            <select
                                class="form-select"
                                id="edit_lawyer_id">

                                @foreach($lawyers as $lawyer)

                                    <option
                                        value="{{ $lawyer->id }}"
                                        @selected(
                                            $case->lawyer_id == $lawyer->id
                                        )>

                                        {{ $lawyer->name }}

                                    </option>

                                @endforeach

                            </select>

                        </div>

                    @endif

                </form>

            </div>

            <div class="modal-footer">

                <button
                    class="btn btn-primary"
                    id="btnSaveCase">

                    Guardar

                </button>

            </div>

        </div>

    </div>

</div>

@include('cases.partials.modal-financial')

@endsection


@push('scripts')
<script>

// ==========================================
// RECORDAR PESTAÑA ACTIVA
// ==========================================

document.addEventListener('DOMContentLoaded', function () {

    let activeTab = localStorage.getItem('caseActiveTab');

    if (activeTab) {

        let trigger = document.querySelector(
            '[data-bs-target="' + activeTab + '"]'
        );

        if (trigger) {

            new bootstrap.Tab(trigger).show();

        }

    }

    document.querySelectorAll(
        '#caseTabs button[data-bs-toggle="tab"]'
    ).forEach(function(tab){

        tab.addEventListener('shown.bs.tab', function(e){

            localStorage.setItem(
                'caseActiveTab',
                e.target.dataset.bsTarget
            );

        });

    });

});

// ==========================================
// ABRIR MODAL EDITAR CASO
// ==========================================

$('#btnEditCase').click(function(){

    $('#modalEditCase').modal('show');

});

// ==========================================
// GUARDAR EDICIÓN CASO
// ==========================================

$('#btnSaveCase').click(function(){

    $.ajax({

        url: `/cases/${caseId}/quick-update`,

        method: 'PUT',

        data: {

            _token: '{{ csrf_token() }}',

            court_name:
                $('#edit_court_name').val(),

            case_number:
                $('#edit_case_number').val(),

            title:
                $('#edit_title').val(),

            description:
                $('#edit_description').val(),

            lawyer_id:
                $('#edit_lawyer_id').val(),

        },

        success: function(){

            // ====================================
            // ACTUALIZAR DOM
            // ====================================

            $('#case-court-name').text(
                $('#edit_court_name').val() || '-'
            );

            $('#case-case-number').text(
                $('#edit_case_number').val() || '-'
            );

            $('#case-title').text(
                $('#edit_title').val()
            );

            $('#case-description').text(
                $('#edit_description').val()
                || 'Sin descripción'
            );

            // ====================================
            // ABOGADO
            // ====================================

            let lawyerText = $('#edit_lawyer_id option:selected').text();

            if(lawyerText){

                $('#case-lawyer').text(
                    lawyerText
                );

            }

            // ====================================
            // CERRAR MODAL
            // ====================================

            $('#modalEditCase').modal('hide');

        },

        error: function(xhr){

            console.log(xhr.responseText);

            alert('Error al actualizar');

        }

    });

});


let canManageCaseContent = @json($canManageCaseContent);
let caseId = {{ $case->id }};

// =============================
// CAMBIAR ESTADO
// =============================
$(document).on('click', '.btn-change-status', function(){

    let status = $(this).data('status');

    if(confirm('¿Cambiar estado del caso?')){

        $.post("{{ route('cases.change-status', $case->id) }}", {
            _token: '{{ csrf_token() }}',
            status: status
        }, function(){
            location.reload();
        });

    }

});


// =====================================================
// FORMATEAR FECHA
// =====================================================

function formatDate(date){

    if(!date) return '';

    const d = new Date(date);

    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');

    return `${year}-${month}-${day}`;
}

// =====================================================
// FORMATEAR HORA
// =====================================================

function formatTime(date){

    if(!date) return '';

    const d = new Date(date);

    const hour = String(d.getHours()).padStart(2, '0');
    const min = String(d.getMinutes()).padStart(2, '0');

    return `${hour}:${min}`;
}

// =====================================================
// REDONDEA HORA ACTUAL
// =====================================================

function roundToNext15Minutes(date){

    let d = new Date(date);

    d.setSeconds(0);
    d.setMilliseconds(0);

    let minutes = d.getMinutes();

    let rounded = Math.ceil(minutes / 15) * 15;

    // 🔥 si llega a 60
    if(rounded === 60){

        d.setHours(d.getHours() + 1);
        d.setMinutes(0);

    } else {

        d.setMinutes(rounded);

    }

    return d;
}

// =====================================================
// SUMAR 15 MINUTOS
// =====================================================

function add15Minutes(time){

    let [hour, min] = time.split(':');

    let date = new Date();

    date.setHours(hour);
    date.setMinutes(min);

    date.setMinutes(date.getMinutes() + 15);

    let h = String(date.getHours()).padStart(2, '0');
    let m = String(date.getMinutes()).padStart(2, '0');

    return `${h}:${m}`;
}

// =====================================================
// SUMAR 60 MINUTOS
// =====================================================

function add60Minutes(time){

    let [hour, min] = time.split(':');

    let date = new Date();

    date.setHours(hour);
    date.setMinutes(min);

    date.setMinutes(date.getMinutes() + 60);

    let h = String(date.getHours()).padStart(2, '0');
    let m = String(date.getMinutes()).padStart(2, '0');
    console.log(`now: ${date}, hora: ${h}:${m}`)
    return `${h}:${m}`;
}



</script>
@endpush