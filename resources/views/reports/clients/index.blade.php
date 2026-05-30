@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <div class="card shadow-sm">

        <div class="card-header">

            <h4 class="mb-0">

                Reporte de Clientes

            </h4>

        </div>

        <div class="card-body">

            @include('reports.clients.partials.filters')

            @include('reports.clients.partials.kpis')

            @include('reports.clients.partials.charts')

            @include('reports.clients.partials.tables')

        </div>

    </div>

</div>

@endsection

@include('reports.clients.partials.scripts')