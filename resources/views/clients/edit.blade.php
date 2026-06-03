@extends('layouts.app')

@section('content')

<div class="card border-0 shadow-sm">
	<div class="card-header bg-white">
		<h6>Editar cliente</h6>
	</div>
	<div class="card-body">
		<form method="POST" action="{{ route('clients.update',$client) }}" class="form-loading">
			@csrf
			@method('PUT')
			@include('clients._form')
			<x-form.button>
				<i class="bi bi-save"></i> Actualizar
			</x-form.button>
		</form>
	</div>
</div>

@endsection