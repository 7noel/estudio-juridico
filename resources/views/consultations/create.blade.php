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

<div class="modal fade" id="modalQuickClient">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    Nuevo cliente
                </h5>
                <button class="btn-close"
                        data-bs-dismiss="modal">
                </button>
            </div>
            <div class="modal-body">
                <form id="quickClientForm">
                    @csrf
                    @include('clients._form')
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="btnSaveQuickClient">
                    <i class="bi bi-save"></i> Guardar cliente
                </button>
            </div>
        </div>
    </div>
</div>

@endsection