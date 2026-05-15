@extends('layouts.app')

@section('content')

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        Editar Consulta
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('consultations.update', $consultation->id) }}">
            @csrf
            @method('PUT')
            @include('consultations._form')
            <x-form.button>
                <i class="bi bi-save"></i> Actualizar
            </x-form.button>
        </form>
    </div>
</div>
@include('consultations.partials.payments')

@endsection