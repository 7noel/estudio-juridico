@extends('layouts.guest')

@section('content')

<h5 class="text-center mb-4">

Confirmar contraseña

</h5>

<p class="text-muted small">

Por seguridad, confirma tu contraseña
antes de continuar.

</p>

<form method="POST"
      action="{{ route('password.confirm') }}">

@csrf

<x-form.input
    name="password"
    type="password"
    label="Contraseña"
    required
/>

<x-form.button
class="btn btn-outline-primary w-100">

<i class="bi bi-shield-lock"></i>

Confirmar

</x-form.button>

</form>

@endsection