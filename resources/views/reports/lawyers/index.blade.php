@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <div class="card shadow-sm">

        <div class="card-header">

            <h4 class="mb-0">

                Reporte de Abogados

            </h4>

        </div>

        <div class="card-body">

            @include('reports.lawyers.partials.filters')

            @include('reports.lawyers.partials.kpis')

            @include('reports.lawyers.partials.charts')

            @include('reports.lawyers.partials.tables')

        </div>

    </div>

</div>

@endsection

@include('reports.lawyers.partials.scripts')