@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <div class="card shadow-sm">

        <div class="card-header">

            <h4 class="mb-0">
                Reporte de Seguimiento y Conversión
            </h4>

        </div>

        <div class="card-body">

            @include('reports.conversion.partials.filters')

            @include('reports.conversion.partials.kpis')

            @include('reports.conversion.partials.charts')

            @include('reports.conversion.partials.tables')

        </div>

    </div>

</div>

@endsection

@include('reports.conversion.partials.scripts')