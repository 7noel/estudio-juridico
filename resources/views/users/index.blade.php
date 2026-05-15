@extends('layouts.app')

@section('content')

<div class="card border-0 shadow-sm">
	<div class="card-header bg-white d-flex justify-content-between">
		<h6 class="mb-0">
			<i class="bi bi-person"></i> Usuarios
		</h6>
		@can('create users')
		<a href="{{ route('users.create') }}" class="btn btn-outline-primary btn-sm">
			<i class="bi bi-plus"></i> Nuevo
		</a>
		@endcan
	</div>
	<div class="card-body">
		<div class="table-responsive">
			<table id="usersTable" class="table table-sm table-bordered table-striped"></table>
		</div>
	</div>
</div>

@endsection


@push('scripts')

<script>

let table;

$(function(){
	$('#usersTable').DataTable({
		processing: true,
		serverSide: true,
		ajax: "{{ route('users.index') }}",
		columns: [
			{ data: 'id', title: 'ID' },
			{ data: 'name', title: 'Nombre' },
			{ data: 'email', title: 'Correo' },
			{ data: 'role', title: 'Rol' },
			{ data: 'actions', title: 'Acciones', orderable: false, searchable: false }
		],
        columnDefs: [
            { className: "text-center", targets: [0, 2, 3, 4] },
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