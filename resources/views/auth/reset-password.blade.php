@extends('layouts.guest')

@section('content')

<h5 class="text-center mb-4">

Restablecer contraseña

</h5>

<form method="POST"
      action="{{ route('password.update') }}">

@csrf

<input
type="hidden"
name="token"
value="{{ $request->route('token') }}">

<x-form.input
    name="email"
    type="email"
    label="Correo electrónico"
    required
/>

<x-form.input
    name="password"
    type="password"
    label="Nueva contraseña"
    required
/>

<x-form.input
    name="password_confirmation"
    type="password"
    label="Confirmar contraseña"
    required
/>

<x-form.button
class="btn btn-outline-primary w-100">

<i class="bi bi-key"></i>

Restablecer

</x-form.button>

</form>

@endsection