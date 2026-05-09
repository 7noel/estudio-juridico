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
                    <i class="bi bi-check-circle-fill"></i> Cerrar Caso
                </button>
            @endif

        </div>

    </div>

    {{-- BODY --}}
    <div class="card-body">

        {{-- INFO PRINCIPAL --}}
        <div class="card mb-3">
            <div class="card-header">Información del caso</div>

            <div class="card-body row">

                <div class="col-md-3">
                    <strong>Cliente:</strong><br>
                    {{ $case->client->full_name ?? '-' }}
                </div>

                <div class="col-md-3">
                    <strong>Abogado:</strong><br>
                    {{ $case->lawyer->name ?? '-' }}
                </div>

                <div class="col-md-3">
                    <strong>Tipo:</strong><br>
                    {{ config('options.service_types')[$case->service_type] ?? '-' }}
                </div>

                <div class="col-md-3">
                    <strong>Estado:</strong><br>
                    <span class="badge bg-{{ config('options.case_status_colors')[$case->status] }}">
                        {{ config('options.case_statuses')[$case->status] }}
                    </span>
                </div>

                <div class="col-md-6 mt-3">
                    <strong>Especialidad:</strong><br>
                    {{ $case->specialty->name ?? '-' }}
                </div>

                <div class="col-md-6 mt-3">
                    <strong>Materia:</strong><br>
                    {{ $case->subject->name ?? '-' }}
                </div>

                <div class="col-md-12 mt-3">
                    <strong>Título:</strong><br>
                    {{ $case->title }}
                </div>

                <div class="col-md-12 mt-3">
                    <strong>Descripción:</strong><br>
                    {{ $case->description ?? 'Sin descripción' }}
                </div>

            </div>
        </div>

        @if($canViewCaseContent)
            @include('cases.partials.activities')
            @include('cases.partials.documents')
            @include('cases.partials.events')
        @else
            <div class="alert alert-warning">
                Debes iniciar el caso para registrar
                actividades, documentos y agenda.
            </div>
        @endif

    </div>

</div>


@endsection


@push('scripts')
<script>
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