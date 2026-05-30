@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <div class="card shadow-sm">

        <div class="card-header">

            <h4 class="mb-0">

                Reporte de Agenda

            </h4>

        </div>

        <div class="card-body">

            @include('reports.agenda.partials.filters')

            @include('reports.agenda.partials.kpis')

            @include('reports.agenda.partials.charts')

            @include('reports.agenda.partials.tables')

        </div>

    </div>

</div>

@endsection

@include('reports.agenda.partials.scripts')