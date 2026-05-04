@extends('layouts.app')

@section('content')

<div class="card border-0 shadow-sm">
	<div class="card-header bg-white">
		<h6>Editar especialidad</h6>
	</div>
	<div class="card-body">
		<form method="POST" action="{{ route('legal-specialties.update',$legalSpecialty) }}">
			@csrf
			@method('PUT')
			@include('legal_specialties._form')
			<x-form.button>
				Actualizar
			</x-form.button>
		</form>
	</div>
</div>

@endsection