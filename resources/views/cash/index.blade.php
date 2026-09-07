@extends('layouts.app')

@section('title', 'Caja')

@section('content')

<div class="container-fluid">

    <div class="card mb-4">

        <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">

            <h4 class="mb-0">

                <i class="bi bi-wallet2"></i>

                Caja Chica

            </h4>

            <div class="d-flex gap-2">

                <button
                    type="button"
                    class="btn btn-success btn-sm"
                    data-bs-toggle="modal"
                    data-bs-target="#modalIncome">

                    <i class="bi bi-plus-circle"></i>

                    Registrar Ingreso

                </button>

                <button
                    type="button"
                    class="btn btn-danger btn-sm"
                    data-bs-toggle="modal"
                    data-bs-target="#modalCashExpense">

                    <i class="bi bi-plus-circle"></i>

                    Registrar Gasto

                </button>

            </div>

        </div>

    </div>

    @include('cash.partials.filters')

    @include('cash.partials.kpis')

    @include('cash.partials.charts')

    @include('cash.partials.table')

</div>

@include('cash.partials.modals')

{{-- Toast de notificación --}}

<div
    class="toast-container position-fixed bottom-0 end-0 p-3"
    style="z-index:1080;">

    <div
        id="cashToast"
        class="toast align-items-center text-bg-success border-0"
        role="alert"
        aria-live="assertive"
        aria-atomic="true">

        <div class="d-flex">

            <div class="toast-body" id="cashToastMessage"></div>

            <button
                type="button"
                class="btn-close btn-close-white me-2 m-auto"
                data-bs-dismiss="toast"
                aria-label="Cerrar"></button>

        </div>

    </div>

</div>

@endsection

@include('cash.partials.scripts')
