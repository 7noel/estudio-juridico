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

{{ $client->document_type_text }} {{ $client->document_number }}

@if($client->is_temporary_document)
    <span class="badge bg-warning text-dark">TMP</span>
@endif

</p>

<p>

<strong>Celular:</strong>

{{ $client->mobile }}

</p>

</div>

</div>

@endsection