@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <div class="card shadow-sm">

        <div class="card-header">

            <h4 class="mb-0">
                Reporte Financiero
            </h4>

        </div>

        <div class="card-body">

            @include('reports.financial.partials.filters')

            @include('reports.financial.partials.kpis')

            @include('reports.financial.partials.charts')

            @include('reports.financial.partials.tables')

        </div>

    </div>

</div>

@endsection

@include('reports.financial.partials.scripts')