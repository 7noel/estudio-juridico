@extends('layouts.app')

@section('content')

<div class="container">

    <div class="card border-0 shadow-sm">

        <div class="card-header">
            Nueva Configuración
        </div>

        <div class="card-body">

            <form method="POST" action="{{ route('notification-settings.store') }}" class="form-loading">
                @csrf
                <div class="mb-3">
                    <label> Key </label>
                    <input type="text" name="key" class="form-control">
                </div>
                <div class="mb-3">
                    <label> Label </label>
                    <input type="text" name="label" class="form-control">
                </div>
                <div class="mb-3">
                    <label> Value </label>
                    <input type="text" name="value" class="form-control">
                </div>
                <div class="mb-3">
                    <label> Type </label>
                    <select name="type" class="form-control">
                        <option value="string">
                            String
                        </option>
                        <option value="integer">
                            Integer
                        </option>
                        <option value="boolean">
                            Boolean
                        </option>
                        <option value="time">
                            Time
                        </option>
                    </select>
                </div>

                <button class="btn btn-primary">
                    Guardar
                </button>
            </form>
        </div>
    </div>
</div>

@endsection