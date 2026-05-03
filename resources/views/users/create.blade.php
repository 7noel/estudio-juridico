@extends('layouts.app')

@section('content')

<div class="card border-0 shadow-sm">
	<div class="card-header bg-white">
		<h6> Nuevo usuario </h6>
	</div>
	<div class="card-body">
		<form method="POST" action="{{ route('users.store') }}">
			@csrf
			@include('users._form')
			<x-form.button>
				<i class="bi bi-save"></i>Guardar
			</x-form.button>
		</form>
	</div>
</div>

@endsection