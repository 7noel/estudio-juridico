@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <div class="card shadow-sm">

        <div class="card-header">

            <h4 class="mb-0">

                Reporte Operativo

            </h4>

        </div>

        <div class="card-body">

            @include('reports.operational.partials.filters')

            @include('reports.operational.partials.kpis')

            @include('reports.operational.partials.charts')

            @include('reports.operational.partials.tables')

        </div>

    </div>

</div>

@endsection

@include('reports.operational.partials.scripts')