@extends('layouts.app')

@section('content')

<div class="row g-3">

{{-- Consultas --}}

<div class="col-md-3">

<div class="card border-0 shadow-sm">

<div class="card-body">

<div class="d-flex justify-content-between">

<div>

<h6 class="text-muted">

Consultas

</h6>

<h4>

0

</h4>

</div>

<div class="text-primary">

<i class="bi bi-chat-left-text fs-2"></i>

</div>

</div>

</div>

</div>

</div>

{{-- Casos --}}

<div class="col-md-3">

<div class="card border-0 shadow-sm">

<div class="card-body">

<div class="d-flex justify-content-between">

<div>

<h6 class="text-muted">

Casos activos

</h6>

<h4>

0

</h4>

</div>

<div class="text-success">

<i class="bi bi-folder fs-2"></i>

</div>

</div>

</div>

</div>

</div>

{{-- Agenda --}}

<div class="col-md-3">

<div class="card border-0 shadow-sm">

<div class="card-body">

<div class="d-flex justify-content-between">

<div>

<h6 class="text-muted">

Eventos hoy

</h6>

<h4>

0

</h4>

</div>

<div class="text-warning">

<i class="bi bi-calendar-event fs-2"></i>

</div>

</div>

</div>

</div>

</div>

{{-- Pagos --}}

<div class="col-md-3">

<div class="card border-0 shadow-sm">

<div class="card-body">

<div class="d-flex justify-content-between">

<div>

<h6 class="text-muted">

Pagos pendientes

</h6>

<h4>

0

</h4>

</div>

<div class="text-danger">

<i class="bi bi-cash-stack fs-2"></i>

</div>

</div>

</div>

</div>

</div>

</div>

@endsection