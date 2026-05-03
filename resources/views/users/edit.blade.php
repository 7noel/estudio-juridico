@extends('layouts.app')

@section('content')

<div class="card border-0 shadow-sm">

<div class="card-header bg-white">

<h6>

Editar usuario

</h6>

</div>

<div class="card-body">

<form
method="POST"
action="{{ route('users.update',$user) }}">

@csrf
@method('PUT')

@include('users._form')

<x-form.button>

Actualizar

</x-form.button>

</form>

</div>

</div>

@endsection