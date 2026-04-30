@extends('layouts.guest')

@section('content')

<h5 class="text-center mb-4">

Verificación de correo

</h5>

<div class="alert alert-light border">

Se ha enviado un enlace de verificación
a tu correo electrónico.

Revisa tu bandeja de entrada.

</div>

<form method="POST"
      action="{{ route('verification.send') }}">

@csrf

<x-form.button
class="btn btn-outline-secondary w-100">

<i class="bi bi-envelope-arrow-up"></i>

Reenviar correo

</x-form.button>

</form>

@endsection