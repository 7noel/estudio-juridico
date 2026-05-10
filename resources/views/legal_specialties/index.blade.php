@extends('layouts.app')

@section('content')

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between">
        <h6 class="mb-0">
            <i class="bi-bookmarks"></i> Especialidades
        </h6>

        <a href="{{ route('legal-specialties.create') }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-plus"></i> Nuevo
        </a>
    </div>

    <div class="card-body">
        <table id="table" class="table table-sm table-bordered table-striped"></table>
    </div>
</div>

@endsection

@push('scripts')
<script>
$('#table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('legal-specialties.data') }}",
    columns: [
        {data:'id', title:'ID'},
        {data:'name', title:'Especialidad'},
        {data:'subjects_count', title:'Materias'},
        {data:'actions', title:'Acciones'}
    ],
    scrollX: true,
    autoWidth: false,
    pageLength: 50,
    language: {
        url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
    }
});
</script>
@endpush