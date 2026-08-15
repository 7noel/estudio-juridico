@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0"><i class="bi bi-person-badge"></i> Detalle de usuario</h5>
    <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary">
        <i class="bi bi-pencil"></i> Editar
    </a>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white">
        <h6 class="mb-0">Información del usuario</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3 mb-2"><strong>Nombre:</strong><p class="mb-0">{{ $user->name }}</p></div>
            <div class="col-md-3 mb-2"><strong>Email:</strong><p class="mb-0">{{ $user->email }}</p></div>
            <div class="col-md-3 mb-2"><strong>Celular:</strong><p class="mb-0">{{ $user->mobile ?: '-' }}</p></div>
            <div class="col-md-3 mb-2"><strong>Rol:</strong><p class="mb-0">{{ $roles[$user->getRoleNames()->first()] ?? $user->getRoleNames()->first() ?? '-' }}</p></div>
            <div class="col-md-3 mb-2"><strong>Establecimiento:</strong><p class="mb-0">{{ $establishments[$user->establishment_id] ?? '-' }}</p></div>
            <div class="col-md-3 mb-2"><strong>Registrado:</strong><p class="mb-0">{{ $user->created_at?->format('d/m/Y H:i') ?: '-' }}</p></div>
            <div class="col-md-3 mb-2"><strong>Última actualización:</strong><p class="mb-0">{{ $user->updated_at?->format('d/m/Y H:i') ?: '-' }}</p></div>
        </div>
    </div>
</div>

@include('users.partials.audit-by-user', ['audits' => $auditsByUser])

@include('users.partials.audit-to-user', ['audits' => $auditsToUser])

@endsection