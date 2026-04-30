<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1">

<title>
{{ config('app.name') }}
</title>

{{-- Bootstrap 5 --}}

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
rel="stylesheet">

{{-- Icons --}}

<link
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
rel="stylesheet">

<style>

body {

background-color: #f8f9fa;

}

/* Contenedor principal */

.auth-wrapper {

min-height: 100vh;

display: flex;

align-items: center;

justify-content: center;

}

/* Card */

.auth-card {

border-radius: 12px;

box-shadow: 0 0 25px rgba(0,0,0,.05);

}

/* Logo */

.auth-logo {

font-size: 28px;

font-weight: 600;

color: #2c3e50;

}

/* Botones outline */

.btn-outline-primary {

border-width: 2px;

}

/* Responsive móvil */

@media (max-width: 768px) {

.auth-card {

margin: 15px;

}

}

</style>

@stack('styles')

</head>

<body>

<div class="auth-wrapper">

<div class="container">

<div class="row justify-content-center">

<div class="col-md-5 col-lg-4">

{{-- LOGO / TITULO --}}

<div class="text-center mb-4">

<div class="auth-logo">

⚖️ {{ config('app.name') }}

</div>

<small class="text-muted">

Sistema de Gestión Jurídica

</small>

</div>

{{-- CARD --}}

<div class="card auth-card">

<div class="card-body p-4">

@yield('content')

</div>

</div>

{{-- FOOTER --}}

<div class="text-center mt-3">

<small class="text-muted">

© {{ date('Y') }}

</small>

</div>

</div>

</div>

</div>

</div>

<script
src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js">
</script>

@stack('scripts')

</body>

</html>