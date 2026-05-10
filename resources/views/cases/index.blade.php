@extends('layouts.app')

@section('content')

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between">
        <h6 class="mb-2">
            <i class="bi bi-folder"></i> Casos
        </h6>
    </div>

    {{-- FILTROS --}}
    <div class="row mb-3 mt-3">

        {{-- ESTADO --}}
        <div class="col-md">
            <label>Estado</label>
            <select id="filter_status" class="form-control form-control-sm">
                <option value="">Todos</option>
                @foreach(config('options.case_statuses') as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>
        </div>

        {{-- ABOGADO --}}
        <div class="col-md">
            <label>Abogado</label>
            <select id="filter_lawyer" class="form-control form-control-sm">
                <option value="">Todos</option>
                @foreach($lawyers as $lawyer)
                    <option value="{{ $lawyer->id }}">{{ $lawyer->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- TIPO SERVICIO --}}
        <div class="col-md">
            <label>Tipo servicio</label>
            <select id="filter_service_type" class="form-control form-control-sm">
                <option value="">Todos</option>
                @foreach(config('options.service_types') as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>
        </div>

        {{-- ESPECIALIDAD --}}
        <div class="col-md">
            <label>Especialidad</label>
            <select id="filter_specialty" class="form-control form-control-sm">
                <option value="">Todas</option>
                @foreach($specialties as $sp)
                    <option value="{{ $sp->id }}">{{ $sp->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- FECHA DESDE --}}
        <div class="col-md">
            <label>Desde</label>
            <input type="date" id="filter_date_from" class="form-control form-control-sm">
        </div>

        {{-- FECHA HASTA --}}
        <div class="col-md">
            <label>Hasta</label>
            <input type="date" id="filter_date_to" class="form-control form-control-sm">
        </div>

        {{-- LIMPIAR --}}
        <div class="col-md d-flex align-items-end">
            <button id="btn-clear" class="btn btn-outline-secondary btn-sm w-100">
                Limpiar
            </button>
        </div>

    </div>

    {{-- STATS --}}
    <div class="mb-3 d-flex flex-wrap gap-2">

        <span class="badge bg-secondary filter-quick px-3 py-2" data-status="">
            Todos: <span id="stat_all">0</span>
        </span>

        <span class="badge bg-primary filter-quick px-3 py-2" data-status="open">
            Abiertos: <span id="stat_open">0</span>
        </span>

        <span class="badge bg-warning text-dark filter-quick px-3 py-2" data-status="in_progress">
            En proceso: <span id="stat_in_progress">0</span>
        </span>

        <span class="badge bg-secondary filter-quick px-3 py-2" data-status="on_hold">
            Pausados: <span id="stat_on_hold">0</span>
        </span>

        <span class="badge bg-success filter-quick px-3 py-2" data-status="closed">
            Cerrados: <span id="stat_closed">0</span>
        </span>

    </div>

    {{-- TABLA --}}
    <div class="card-body">
        <div class="table-responsive">
            <table id="casesTable" class="table table-sm table-bordered table-striped w-100">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Abogado</th>
                        <th>Tipo</th>
                        <th>Especialidad</th>
                        <th>Materia</th>
                        <th>Consulta</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

</div>

@endsection

@push('scripts')

<script>
$(function(){

    let table = $('#casesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('cases.data') }}",
            data: function(d){
                d.status = $('#filter_status').val();
                d.lawyer_id = $('#filter_lawyer').val();
                d.service_type = $('#filter_service_type').val();
                d.legal_specialty_id = $('#filter_specialty').val();
                d.date_from = $('#filter_date_from').val();
                d.date_to = $('#filter_date_to').val();
            }
        },
        columns: [
            { data: 'id' },
            { data: 'client' },
            { data: 'lawyer' },
            { data: 'service_type' },
            { data: 'specialty' },
            { data: 'subject' },
            { data: 'consultation_link', name: 'consultation.id' },
            { data: 'status' },
            { data: 'opened_at' },
            { data: 'actions', orderable: false, searchable: false }
        ],
        columnDefs: [
            { className: "text-center", targets: [0,3,4,5,6,7,8,9] },
        ],
        scrollX: true,
        pageLength: 50,
        order: [[0, 'desc']],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        }
    });

    // FILTROS
    $('#filter_status, #filter_lawyer, #filter_service_type, #filter_specialty, #filter_date_from, #filter_date_to')
        .change(function(){
            table.draw();
            loadStats();
        });

    // LIMPIAR
    $('#btn-clear').click(function(){
        $('#filter_status').val('');
        $('#filter_lawyer').val('');
        $('#filter_service_type').val('');
        $('#filter_specialty').val('');
        $('#filter_date_from').val('');
        $('#filter_date_to').val('');

        table.draw();
        loadStats();
    });

    // QUICK FILTER
    $('.filter-quick').click(function(){
        let status = $(this).data('status');

        $('#filter_status').val(status);

        $('.filter-quick').removeClass('active');
        $(this).addClass('active');

        table.draw();
        loadStats();
    });

    // STATS
    function loadStats(){

        $.get("{{ route('cases.stats') }}", {
            status: $('#filter_status').val(),
            lawyer_id: $('#filter_lawyer').val(),
            service_type: $('#filter_service_type').val(),
            legal_specialty_id: $('#filter_specialty').val(),
            date_from: $('#filter_date_from').val(),
            date_to: $('#filter_date_to').val(),
            search: table.search()
        }, function(data){

            $('#stat_all').text(data.all);
            $('#stat_open').text(data.open);
            $('#stat_in_progress').text(data.in_progress);
            $('#stat_on_hold').text(data.on_hold);
            $('#stat_closed').text(data.closed);

        });

    }

    // SINCRONIZAR BUSCADOR
    table.on('search.dt', function(){
        loadStats();
    });

    loadStats();

});
</script>

@endpush