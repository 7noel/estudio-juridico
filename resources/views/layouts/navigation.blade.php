<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm">

<div class="container-fluid">

{{-- Toggle sidebar (más adelante lo activamos) --}}

<button class="btn btn-outline-secondary me-2">
<i class="bi bi-list"></i>
</button>

{{-- Nombre del sistema --}}

<span class="navbar-brand">

⚖️ {{ config('app.name') }}

</span>

{{-- Usuario --}}

<div class="ms-auto">

<div class="dropdown">

<button
class="btn btn-outline-secondary dropdown-toggle"
data-bs-toggle="dropdown">

{{-- Nombre usuario --}}

{{ auth()->user()->name }}

{{-- Establecimiento --}}

@if(auth()->user()->employee)

<small class="text-muted d-block">

{{ auth()->user()->employee->establishment->name ?? '' }}

</small>

@endif

</button>

<ul class="dropdown-menu dropdown-menu-end">

<li>

<a
class="dropdown-item"
href="{{ route('profile.edit') }}">

<i class="bi bi-person"></i>

Perfil

</a>

</li>

<li>

<hr class="dropdown-divider">

</li>

<li>

<form method="POST"
action="{{ route('logout') }}">

@csrf

<button
type="submit"
class="dropdown-item">

<i class="bi bi-box-arrow-right"></i>

Cerrar sesión

</button>

</form>

</li>

</ul>

</div>

</div>

</div>

</nav>