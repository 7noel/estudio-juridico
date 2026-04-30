@extends('layouts.guest')

@section('content')

<h5 class="text-center mb-4">

Crear cuenta

</h5>

<form method="POST"
      action="{{ route('register') }}">

@csrf

<x-form.input
    name="name"
    label="Nombre"
    required
/>

<x-form.input
    name="email"
    type="email"
    label="Correo electrónico"
    required
/>

<x-form.input
    name="password"
    type="password"
    label="Contraseña"
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

<i class="bi bi-person-plus"></i>

Registrar

</x-form.button>

<div class="text-center mt-3">

<a
href="{{ route('login') }}"
class="text-decoration-none">

¿Ya tienes cuenta?

</a>

</div>

</form>

@endsection