@extends('layouts.app')

@section('title', 'Reporte de Caja')

@section('content')

<div class="container-fluid">

    <div class="card">

        <div class="card-header">

            <h4 class="mb-0">

                <i class="bi bi-cash-coin"></i>

                Reporte de Caja

            </h4>

        </div>

        <div class="card-body">

            @include('reports.cash.partials.filters')

            @include('reports.cash.partials.kpis')

            @include('reports.cash.partials.charts')

            @include('reports.cash.partials.table')

        </div>

    </div>

</div>

@endsection

@include('reports.cash.partials.scripts')