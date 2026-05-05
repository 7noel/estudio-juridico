@extends('layouts.app')

@section('content')

<div class="card border-0 shadow-sm">
	<div class="card-header bg-white d-flex justify-content-between">
		<h6 class="mb-0">
			<i class="bi bi-shield-lock"></i> Roles
		</h6>
		<a href="{{ route('roles.create') }}" class="btn btn-outline-primary btn-sm">
			<i class="bi bi-plus"></i> Nuevo
		</a>
	</div>
	<div class="card-body">
		<div class="table-responsive">
			<table id="rolesTable" class="table table-sm table-bordered table-striped">
				<thead>
					<tr>
						<th>ID</th>
						<th>Nombre</th>
						<th>Permisos</th>
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
	$('#rolesTable').DataTable({
		processing: true,
		serverSide: true,
		ajax: "{{ route('roles.index') }}",
		columns: [
			{ data: 'id' },
			{ data: 'name' },
			{ data: 'permissions' },
			{
				data: 'actions',
				orderable: false,
				searchable: false
			}
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