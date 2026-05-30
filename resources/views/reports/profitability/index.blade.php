@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <div class="card shadow-sm">

        <div class="card-header">

            <h4 class="mb-0">

                Reporte de Rentabilidad

            </h4>

        </div>

        <div class="card-body">

            @include('reports.profitability.partials.filters')

            @include('reports.profitability.partials.kpis')

            @include('reports.profitability.partials.charts')

            @include('reports.profitability.partials.tables')

        </div>

    </div>

</div>

@endsection

@include('reports.profitability.partials.scripts')