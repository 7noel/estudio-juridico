@extends('layouts.app')

@section('content')

<div class="card shadow-sm">
	<div class="card-header">
		Editar Permiso
	</div>
	<div class="card-body">
		<form method="POST" action="{{ route('permissions.update',$permission) }}">
			@csrf
			@method('PUT')
			@include('permissions._form')
			<button class="btn btn-primary">
				Actualizar
			</button>
		</form>
	</div>
</div>

@endsection