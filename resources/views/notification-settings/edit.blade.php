@extends('layouts.app')

@section('content')

<div class="container">
    <div class="card border-0 shadow-sm">
        <div class="card-header">
            Editar Configuración
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('notification-settings.update', $notificationSetting) }}" class="form-loading">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label> Key </label>
                    <input type="text" class="form-control" value="{{ $notificationSetting->key }}" disabled>
                </div>

                <div class="mb-3">
                    <label> Label </label>
                    <input type="text" name="label" class="form-control" value="{{ $notificationSetting->label }}">
                </div>

                <div class="mb-3">
                    <label> Value </label>
                    <input type="text" name="value" class="form-control" value="{{ $notificationSetting->value }}">
                </div>

                <div class="mb-3">
                    <label> Type </label>
                    <select name="type" class="form-control" >
                        <option value="string" @selected($notificationSetting->type == 'string')>
                            String
                        </option>
                        <option value="integer" @selected($notificationSetting->type == 'integer')>
                            Integer
                        </option>
                        <option value="boolean" @selected($notificationSetting->type == 'boolean')>
                            Boolean
                        </option>
                        <option value="time" @selected($notificationSetting->type == 'time')>
                            Time
                        </option>
                    </select>
                </div>

                <button class="btn btn-primary">
                    Actualizar
                </button>
            </form>
        </div>
    </div>
</div>

@endsection