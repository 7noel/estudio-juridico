@extends('layouts.app')

@section('title', 'Detalle de Actividades de Abogados')

@section('content')

<div class="container-fluid">

    <div class="card">

        <div class="card-header">

            <h4 class="mb-0">

                <i class="bi bi-list-check"></i>

                Detalle de Actividades de Abogados

            </h4>

        </div>

        <div class="card-body">

            @include('reports.activity-detail.partials.filters')

            @include('reports.activity-detail.partials.summary')

            @include('reports.activity-detail.partials.table')

        </div>

    </div>

</div>

@endsection

@include('reports.activity-detail.partials.modal')

@include('reports.activity-detail.partials.scripts')
