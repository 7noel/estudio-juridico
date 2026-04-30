@extends('layouts.app')

@section('content')

<div class="card border-0 shadow-sm">

<div class="card-header bg-white">

Cliente

</div>

<div class="card-body">

<p>

<strong>Nombre:</strong>

{{ $client->full_name }}

</p>

<p>

<strong>Documento:</strong>

{{ $client->document_number }}

</p>

<p>

<strong>Celular:</strong>

{{ $client->mobile }}

</p>

</div>

</div>

@endsection