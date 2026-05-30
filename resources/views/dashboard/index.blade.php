@extends('layouts.app')

@section('content')

<div class="container-fluid">

    {{-- KPIs --}}
    @if($isLawyer)

        @include('dashboard.partials.lawyer-kpis')

    @elseif($isReceptionist)

        @include('dashboard.partials.receptionist-kpis')

    @elseif($isAdmin)

        @include('dashboard.partials.admin-kpis')

    @endif

    <div class="row">

        <div class="col-lg-8">

            @include('dashboard.partials.calendar')

        </div>

        <div class="col-lg-4">

            @include('dashboard.partials.recent-activities')

        </div>

    </div>

    <div class="row mt-4">

        <div class="col-lg-6">

            @include('dashboard.partials.charts', [
                'chartId' => 'casesByStatusChart',
                'title' => 'Casos por Estado'
            ])

        </div>

        <div class="col-lg-6">

            @include('dashboard.partials.charts', [
                'chartId' => 'casesBySpecialtyChart',
                'title' => 'Casos por Especialidad'
            ])

        </div>

    </div>

</div>

@endsection

@include('dashboard.partials.scripts')