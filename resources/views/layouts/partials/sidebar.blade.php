<div
class="sidebar bg-white border-end"
style="width:260px; min-height:100vh;">

<div class="p-3">

<h5 class="text-center fw-semibold text-primary">

⚖️ Jurídico

</h5>

</div>

<hr>

<ul class="nav flex-column px-2">

{{-- Dashboard --}}

<li class="nav-item">

<a
class="nav-link text-dark"
href="{{ route('dashboard') }}">

<i class="bi bi-speedometer2"></i>

Dashboard

</a>

</li>


{{-- CLIENTES --}}

@role('Administrador|Recepcionista')

<li class="nav-item">

<a
class="nav-link text-dark"
href="{{ route('clients.index') }}">

<i class="bi bi-people"></i>

Clientes

</a>

</li>

@endrole


{{-- CONSULTAS --}}

<li class="nav-item">

<a
class="nav-link text-dark"
href="{{ route('consultations.index') }}">

<i class="bi bi-chat-left-text"></i>

Consultas

</a>

</li>


{{-- CASOS --}}

<li class="nav-item">

<a
class="nav-link text-dark"
href="{{ route('cases.index') }}">

<i class="bi bi-folder"></i>

Casos

</a>

</li>


{{-- AGENDA --}}

<li class="nav-item">

<a
class="nav-link text-dark"
href="#">

<i class="bi bi-calendar-event"></i>

Agenda

</a>

</li>


{{-- ADMINISTRACIÓN --}}

@role('Administrador')

<li class="nav-item mt-3">

<small class="text-muted px-2">

Administración

</small>

</li>

<li class="nav-item">

<a
class="nav-link text-dark"
href="#">

<i class="bi bi-building"></i>

Establecimientos

</a>

</li>

<li class="nav-item">

<a
class="nav-link text-dark"
href="#">

<i class="bi bi-person-badge"></i>

Empleados

</a>

</li>

@endrole

</ul>

</div>