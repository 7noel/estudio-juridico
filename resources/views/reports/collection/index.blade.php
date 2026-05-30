@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <div class="card shadow-sm">

        <div class="card-header d-flex justify-content-between align-items-center">

            <h4 class="mb-0">
                Reporte de Cobranza
            </h4>

        </div>

        <div class="card-body">

            @include('reports.collection.partials.filters')

            @include('reports.collection.partials.kpis')

            @include('reports.collection.partials.charts')

            @include('reports.collection.partials.tables')

        </div>

    </div>

</div>

@endsection

@include('reports.collection.partials.scripts')