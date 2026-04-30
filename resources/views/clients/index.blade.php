@extends('layouts.app')

@section('content')

<div class="card border-0 shadow-sm">

<div class="card-header bg-white d-flex justify-content-between">

<h6 class="mb-0">

<i class="bi bi-people"></i>

Clientes

</h6>

<a
href="{{ route('clients.create') }}"
class="btn btn-outline-primary btn-sm">

<i class="bi bi-plus"></i>

Nuevo

</a>

</div>

<div class="card-body">

<table class="table table-sm table-hover">

<thead>

<tr>

<th>Nombre</th>
<th>Documento</th>
<th>Celular</th>
<th></th>

</tr>

</thead>

<tbody>

@foreach($clients as $client)

<tr>

<td>

{{ $client->full_name }}

</td>

<td>

{{ $client->document_number }}

</td>

<td>

{{ $client->mobile }}

</td>

<td class="text-end">

<a
href="{{ route('clients.edit',$client) }}"
class="btn btn-outline-secondary btn-sm">

<i class="bi bi-pencil"></i>

</a>

<form
method="POST"
action="{{ route('clients.destroy',$client) }}"
class="d-inline">

@csrf
@method('DELETE')

<button
class="btn btn-outline-danger btn-sm">

<i class="bi bi-trash"></i>

</button>

</form>

</td>

</tr>

@endforeach

</tbody>

</table>

{{ $clients->links() }}

</div>

</div>

@endsection