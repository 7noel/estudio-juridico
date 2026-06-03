@extends('layouts.app')

@section('content')

<div class="card border-0 shadow-sm">
	<div class="card-header bg-white">
		<h6> Nueva especialidad </h6>
	</div>
	<div class="card-body">
		<form method="POST" action="{{ route('legal-specialties.store') }}" class="form-loading">
			@csrf
			@include('legal_specialties._form')
			<x-form.button>
				<i class="bi bi-save"></i> Guardar
			</x-form.button>
		</form>
	</div>
</div>

@endsection