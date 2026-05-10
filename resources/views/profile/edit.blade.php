@extends('layouts.app')

@section('content')

<div class="row g-3">

{{-- Perfil --}}

<div class="col-md-6">

@include('profile.partials.update-profile-information-form')

</div>

{{-- Password --}}

<div class="col-md-6">

@include('profile.partials.update-password-form')

</div>

{{-- Eliminar usuario --}}

<div class="col-md-6">

{{-- @include('profile.partials.delete-user-form') --}}

</div>

</div>

@endsection