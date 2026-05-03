@extends('layouts.app')

@section('content')

<div class="card shadow-sm">
	<div class="card-header">
		Editar Rol
	</div>
	<div class="card-body">
		<form method="POST" action="{{ route('roles.update',$role) }}">
			@csrf
			@method('PUT')
			@include('roles._form')
			<button class="btn btn-primary">
				Actualizar
			</button>
		</form>
	</div>
</div>

@endsection