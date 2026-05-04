@extends('layouts.app')

@section('content')

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between">
        <h6 class="mb-0">
            <i class="bi bi-journal-text"></i> Consultas
        </h6>

        @can('create', App\Models\Consultation::class)
        <a href="{{ route('consultations.create') }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-plus"></i> Nuevo
        </a>
        @endcan
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="consultationsTable" class="table table-sm table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Abogado</th>
                        <th>Tipo Servicio</th>
                        <th>Especialidad</th>
                        <th>Materia</th>
                        <th>Estado</th>
                        <th>Monto</th>
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

    $('#consultationsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('consultations.data') }}",
        columns: [
            { data: 'id' },
            { data: 'client', name: 'client.full_name' },
            { data: 'lawyer', name: 'lawyer.name' },
            { data: 'service_type' },
            { data: 'specialty', name: 'specialty.name' },
            { data: 'subject', name: 'subject.name' },
            { data: 'status' },
            { data: 'total_amount' },
            { data: 'created_at' },
            { data: 'actions', orderable: false, searchable: false }
        ],
        scrollX: true,
        autoWidth: false,
        pageLength: 50,
        order: [[0, 'desc']],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        }
    });

});
</script>

@endpush