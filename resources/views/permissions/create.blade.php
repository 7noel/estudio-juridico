@extends('layouts.app')

@section('content')

<div class="card shadow-sm">
	<div class="card-header">
		Nuevo Permiso
	</div>
	<div class="card-body">
		<form method="POST" action="{{ route('permissions.store') }}">
			@csrf
			@include('permissions._form')
			<button class="btn btn-primary">
				Guardar
			</button>
		</form>
	</div>
</div>

@endsection