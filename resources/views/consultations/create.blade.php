@extends('layouts.app')

@section('content')

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        Nueva Consulta
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('consultations.store') }}">
            @csrf
            @include('consultations._form')
            <x-form.button>
                <i class="bi bi-save"></i> Guardar
            </x-form.button>
        </form>
    </div>
</div>

@endsection