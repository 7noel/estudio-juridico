@extends('layouts.app')

@section('content')

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h6 class="mb-0">
            <i class="bi bi-shield-check"></i> Auditoría
        </h6>
    </div>
    <div class="card-body">
        <form id="auditFiltersForm" class="row g-3 mb-3">
            <div class="col-md-3">
                <label class="form-label">Entidad</label>
                <select name="entity" id="filterEntity" class="form-select form-select-sm">
                    <option value="">Todas</option>
                    @foreach($entities as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Evento</label>
                <select name="event" id="filterEvent" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <option value="created">Creado</option>
                    <option value="updated">Actualizado</option>
                    <option value="deleted">Eliminado</option>
                    <option value="restored">Restaurado</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Desde</label>
                <input type="date" name="date_from" id="filterDateFrom" class="form-control form-control-sm">
            </div>
            <div class="col-md-2">
                <label class="form-label">Hasta</label>
                <input type="date" name="date_to" id="filterDateTo" class="form-control form-control-sm">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" id="filterBtn" class="btn btn-sm btn-primary">
                    <i class="bi bi-funnel"></i> Filtrar
                </button>
                <button type="button" id="resetBtn" class="btn btn-sm btn-secondary ms-1">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </button>
            </div>
        </form>

        <div class="table-responsive">
            <table id="auditsTable" class="table table-sm table-bordered table-striped"></table>
        </div>
    </div>
</div>

@endsection

@push('scripts')

<script>
let table;

$(function() {
    table = $('#auditsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('audits.datatable') }}",
            data: function(d) {
                d.entity = $('#filterEntity').val();
                d.event = $('#filterEvent').val();
                d.date_from = $('#filterDateFrom').val();
                d.date_to = $('#filterDateTo').val();
            }
        },
        columns: [
            { data: 'created_at', title: 'Fecha' },
            { data: 'user_name', title: 'Usuario' },
            { data: 'entity_label', title: 'Entidad' },
            { data: 'auditable_id', title: 'ID' },
            { data: 'event_badge', title: 'Evento' },
            { data: 'changes', title: 'Cambios' },
            { data: 'ip_address', title: 'IP' },
            { data: 'url', title: 'URL' }
        ],
        columnDefs: [
            { className: "text-center", targets: [0, 1, 2, 3, 4, 6] },
            { orderable: false, targets: [5] }
        ],
        order: [[0, 'desc']],
        scrollX: true,
        autoWidth: false,
        pageLength: 25,
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        }
    });

    $('#filterBtn').on('click', function() {
        table.ajax.reload();
    });

    $('#resetBtn').on('click', function() {
        $('#filterEntity').val('');
        $('#filterEvent').val('');
        $('#filterDateFrom').val('');
        $('#filterDateTo').val('');
        table.ajax.reload();
    });
});
</script>

@endpush