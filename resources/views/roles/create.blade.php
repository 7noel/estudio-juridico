@extends('layouts.app')

@section('content')

<div class="card shadow-sm">
	<div class="card-header">
		Nuevo Rol
	</div>
	<div class="card-body">
		<form method="POST" action="{{ route('roles.store') }}" class="form-loading">
			@csrf
			@include('roles._form')
			<button class="btn btn-primary">
				Guardar
			</button>
		</form>
	</div>
</div>

@endsection