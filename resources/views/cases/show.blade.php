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
            @include('cases.partials.activities')
            @include('cases.partials.documents')
            @include('cases.partials.expenses')
            @include('cases.partials.events')
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

<div class="modal fade" id="financialStatusModal" tabindex="-1">

    <div class="modal-dialog modal-lg">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">

                    Estado financiero

                </h5>

                <button
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body">

                @php($consultation = $case->consultation)

                @if($consultation)

                    <div class="row text-center mb-4">

                        <div class="col-md-4">

                            <small>Total</small>

                            <h5>

                                S/ {{ number_format($consultation->total_amount,2) }}

                            </h5>

                        </div>

                        <div class="col-md-4">

                            <small>Pagado</small>

                            <h5 class="text-success">

                                S/ {{ number_format($consultation->paid_amount,2) }}

                            </h5>

                        </div>

                        <div class="col-md-4">

                            <small>Pendiente</small>

                            <h5 class="text-danger">

                                S/ {{ number_format($consultation->pending_amount,2) }}

                            </h5>

                        </div>

                    </div>

                    <table class="table table-bordered table-sm">

                        <thead>

                            <tr>

                                <th>#</th>

                                <th>Monto</th>

                                <th>Pagado</th>

                                <th>Saldo</th>

                                <th>Vence</th>

                                <th>Estado</th>

                            </tr>

                        </thead>

                        <tbody>

                            @foreach($consultation->installments as $installment)

                                <tr>

                                    <td>

                                        {{ $installment->installment_number }}

                                    </td>

                                    <td>

                                        {{ number_format($installment->amount,2) }}

                                    </td>

                                    <td>

                                        {{ number_format($installment->paid_amount,2) }}

                                    </td>

                                    <td>

                                        {{ number_format($installment->pending_amount,2) }}

                                    </td>

                                    <td>

                                        {{ $installment->due_date->format('d/m/Y') }}

                                    </td>

                                    <td>

                                        @if($installment->is_paid)

                                            <span class="badge bg-primary">

                                                Pagado

                                            </span>

                                        @elseif($installment->paid_amount > 0)

                                            <span class="badge bg-warning text-dark">

                                                Parcial

                                            </span>

                                        @elseif($installment->due_date->isPast())

                                            <span class="badge bg-danger">

                                                Vencida

                                            </span>

                                        @else

                                            <span class="badge bg-secondary">

                                                Pendiente

                                            </span>

                                        @endif

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                @endif

            </div>

        </div>

    </div>

</div>

@endsection


@push('scripts')
<script>

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