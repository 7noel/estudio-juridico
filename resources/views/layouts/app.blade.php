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

.ui-autocomplete {

max-height: 300px;
overflow-y: auto;
overflow-x: hidden;
width: auto !important;
min-width: 350px;

border-radius: 4px;
border: 1px solid #dee2e6;

background: white;

font-size: 12px;

padding: 0;

z-index: 9999;

}

/* Items */

.ui-menu-item {

padding: 4px 8px;

border-bottom: 1px solid #f1f1f1;
font-size: 12px;

cursor: pointer;

line-height: 1.2;

}

/* Hover */

.ui-menu-item:hover {

background-color: #f8f9fa;

}

/* Seleccionado */

.ui-state-active {

background-color: #0d6efd !important;
color: white !important;

}

.sidebar {

width: 260px;
transition: margin-left 0.3s ease;

}

.sidebar.collapsed {

margin-left: -260px;

}
</style>

@stack('styles')

</head>

<body>

<div class="d-flex">

    {{-- SIDEBAR --}}

    @include('layouts.partials.sidebar')

    {{-- CONTENT AREA --}}

    <div class="flex-grow-1">

        {{-- NAVBAR --}}

        @include('layouts.partials.navbar')

        {{-- ALERTS --}}

        <div class="container-fluid mt-3">

            @include('layouts.partials.alerts')

            @yield('content')

        </div>

    </div>

</div>

{{-- jQuery --}}

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

{{-- jQuery UI --}}

<script src="https://code.jquery.com/ui/1.13.3/jquery-ui.min.js"></script>

<link
href="https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css"
rel="stylesheet">

{{-- Bootstrap JS --}}

<script
src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js">
</script>

<script>

$(function(){

$('#toggleSidebar').click(function(){
    console.log('toogle')
    $('.sidebar').toggleClass('collapsed');

});

});

</script>

@stack('scripts')

</body>

</html>
