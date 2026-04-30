@extends('layouts.guest')

@section('content')

<h5 class="text-center mb-4">

Recuperar contraseña

</h5>

<p class="text-muted small">

Ingresa tu correo electrónico y te enviaremos
un enlace para restablecer tu contraseña.

</p>

<form method="POST"
      action="{{ route('password.email') }}">

@csrf

<x-form.input
    name="email"
    type="email"
    label="Correo electrónico"
    required
/>

<x-form.button
class="btn btn-outline-primary w-100">

<i class="bi bi-envelope"></i>

Enviar enlace

</x-form.button>

</form>

@endsection