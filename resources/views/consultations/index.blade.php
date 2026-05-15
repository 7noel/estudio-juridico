@extends('layouts.app')

@section('content')

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between">
        <h6 class="mb-0">
            <i class="bi bi-chat-left-text"></i> Consultas
        </h6>

        @can('create', App\Models\Consultation::class)
        <a href="{{ route('consultations.create') }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-plus"></i> Nuevo
        </a>
        @endcan
    </div>

    <div class="row mb-3 mt-3">

        {{-- Estado --}}
        <div class="col-md">
            <label>Estado</label>
            <select id="filter_status" class="form-control form-control-sm">
                <option value="">Todos</option>
                @foreach(config('options.consultation_statuses') as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>

        {{-- Abogado --}}
        <div class="col-md">
            <label>Abogado</label>
            <select id="filter_lawyer" class="form-control form-control-sm">
                <option value="">Todos</option>
                @foreach($lawyers as $lawyer)
                    <option value="{{ $lawyer->id }}">{{ $lawyer->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Tipo de servicio --}}
        <div class="col-md">
            <label>Tipo de servicio</label>
            <select id="filter_service_type" class="form-control form-control-sm">
                <option value="">Todos</option>
                @foreach(config('options.service_types') as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>
        </div>

        {{-- Especialidad --}}
        <div class="col-md">
            <label>Especialidad</label>
            <select id="filter_specialty" class="form-control form-control-sm">
                <option value="">Todas</option>
                @foreach($specialties as $sp)
                    <option value="{{ $sp->id }}">{{ $sp->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Fecha desde --}}
        <div class="col-md">
            <label>Desde</label>
            <input type="date" id="filter_from" class="form-control form-control-sm">
        </div>

        {{-- Fecha hasta --}}
        <div class="col-md">
            <label>Hasta</label>
            <input type="date" id="filter_to" class="form-control form-control-sm">
        </div>

        {{-- Botón limpiar --}}
        <div class="col-md d-flex align-items-end">
            <button id="btn-clear-filters" class="btn btn-outline-secondary btn-sm w-100">
                Limpiar
            </button>
        </div>

    </div>

    <div class="mb-3 d-flex flex-wrap gap-2">

        <span class="badge bg-secondary filter-quick p-2" data-status="">
            Todas: <span id="stat_all">0</span>
        </span>
        <span class="badge bg-primary filter-quick p-2" data-status="">
            Asignados: <span id="stat_assigned">0</span>
        </span>

        <span class="badge bg-warning text-dark filter-quick p-2" data-status="quoted">
            Cotizados: <span id="stat_quoted">0</span>
        </span>

        <span class="badge bg-success filter-quick p-2" data-status="accepted">
            Aceptados: <span id="stat_accepted">0</span>
        </span>

        <span class="badge bg-danger filter-quick p-2" data-status="rejected">
            Rechazados: <span id="stat_rejected">0</span>
        </span>

    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="consultationsTable" class="table table-sm table-bordered table-striped"></table>
        </div>
    </div>
</div>

@endsection


@push('scripts')

<script>
let table; // 🔥 GLOBAL

$(function(){


    table = $('#consultationsTable').DataTable({
        processing: true,
        serverSide: true,

        ajax: {
            url: "{{ route('consultations.data') }}", // 👈 mantenemos tu ruta
            data: function(d){
                d.status = $('#filter_status').val();
                d.lawyer_id = $('#filter_lawyer').val();
                d.date_from = $('#filter_from').val();
                d.date_to = $('#filter_to').val();
                d.service_type = $('#filter_service_type').val();
                d.legal_specialty_id = $('#filter_specialty').val();
            }
        },

        columns: [
            { data: 'id', title: 'ID' },
            { data: 'client', name: 'client.full_name', title: 'Cliente' },
            { data: 'lawyer', name: 'lawyer.name', title: 'Abogado' },
            { data: 'service_type', title: 'Tipo Servicio' },
            { data: 'specialty', name: 'specialty.name', title: 'Especialidad' },
            { data: 'subject', name: 'subject.name', title: 'Materia' },
            { data: 'status', title: 'Estado' },
            { data: 'case_link', name: 'case.id', title: 'Caso' },
            { data: 'total_amount', title: 'Monto' },
            { data: 'created_at', title: 'Fecha' },
            { data: 'actions', title: 'Acciones', orderable: false, searchable: false }
        ],

        columnDefs: [
            { className: "text-center", targets: [0, 3, 4, 5, 6, 7, 9, 10] },
            { className: "text-end", targets: [8] },
        ],

        scrollX: true,
        autoWidth: false,
        pageLength: 50,
        order: [[0, 'desc']],

        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        }
    });

    // 🔥 EVENTOS DE FILTRO

    $('#filter_status, #filter_lawyer, #filter_from, #filter_to').change(function(){
        table.ajax.reload();
    });

    $('#btn-clear-filters').click(function(){

        $('#filter_status').val('');
        $('#filter_lawyer').val('');
        $('#filter_from').val('');
        $('#filter_to').val('');

        table.ajax.reload();
    });

    $('.filter-quick').click(function(){

        let status = $(this).data('status');

        $('#filter_status').val(status);

        table.ajax.reload();
    });

});

$(document).on('click', '.btn-delete', function(){
    let id = $(this).data('id');

    if(confirm('Eliminar?')){
        $.ajax({
            url: '/consultations/' + id,
            type: 'DELETE',
            data: {_token: '{{ csrf_token() }}'},
            success: () => location.reload()
        });
    }
});

$(document).on('click', '.btn-generate-case', function(){

    let url = $(this).data('url');

    if(confirm('¿Desea generar el caso?')){

        $.post(url, {
            _token: '{{ csrf_token() }}'
        }, function(){
            location.reload();
        });

    }

});

// cambio en filtros
$('#filter_status, #filter_lawyer, #filter_service_type, #filter_specialty, #filter_from, #filter_to').change(function(){
    table.ajax.reload();
    loadStats();
});

// limpiar filtros
$('#btn-clear-filters').click(function(){
    $('#filter_status').val('');
    $('#filter_lawyer').val('');
    $('#filter_from').val('');
    $('#filter_to').val('');
    $('#filter_service_type').val('');
    $('#filter_specialty').val('');

    table.ajax.reload();
});

// botones rápidos
$('.filter-quick').click(function(){

    let status = $(this).data('status');

    $('#filter_status').val(status);

    table.ajax.reload();
    loadStats();

});

function loadStats(){

    let searchValue = '';

    if (table) {
        searchValue = table.search();
    }

    $.get("{{ route('consultations.stats') }}", {
        status: $('#filter_status').val(),
        lawyer_id: $('#filter_lawyer').val(),
        date_from: $('#filter_from').val(),
        date_to: $('#filter_to').val(),
        search: searchValue
    }, function(res){

        $('#stat_all').text(res.all);
        $('#stat_assigned').text(res.assigned);
        $('#stat_quoted').text(res.quoted);
        $('#stat_accepted').text(res.accepted);
        $('#stat_rejected').text(res.rejected);

    });

}

loadStats();

$('#consultationsTable').on('search.dt', function () {
    loadStats();
});

</script>

@endpush