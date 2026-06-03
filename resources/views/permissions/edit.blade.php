@extends('layouts.app')

@section('content')

<div class="card shadow-sm">
	<div class="card-header">
		Editar Permiso
	</div>
	<div class="card-body">
		<form method="POST" action="{{ route('permissions.update',$permission) }}" class="form-loading">
			@csrf
			@method('PUT')
			@include('permissions._form')
			<button class="btn btn-primary">
				<i class="bi bi-save"></i> Actualizar
			</button>
		</form>
	</div>
</div>

@endsection