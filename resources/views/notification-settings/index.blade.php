@extends('layouts.app')

@section('content')

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between">
        <h6 class="mb-0">
            <i class="bi bi-people"></i> Clientes
        </h6>
        <a href="{{ route('notification-settings.create') }}" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-plus"></i> Nuevo
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-bordered table-striped w-100" id="tableNotificationSettings"> </table>
        </div>
    </div>

</div>

@endsection

@push('scripts')

<script>

$(function(){

    $('#tableNotificationSettings').DataTable({

        processing: true,

        serverSide: true,

        ajax: '{{ route("notification-settings.datatable") }}',

        columns: [

            { data: 'id', title: 'ID' },
            { data: 'key', title: 'Clave' },
            { data: 'label', title: 'Etiqueta' },
            { data: 'value', title: 'Valor' },
            { data: 'type', title: 'Tipo' },
            { data: 'actions', title: 'Acciones', orderable: false, searchable: false }

        ],
        columnDefs: [
            { className: "text-center", targets: [0, 3, 4,5] },
        ],

        scrollX: true,
        autoWidth: false,
        pageLength: 50,
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        }

    });

});

</script>

@endpush