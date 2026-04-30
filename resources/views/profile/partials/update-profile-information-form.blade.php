<div class="card border-0 shadow-sm">

<div class="card-header bg-white">

<h6 class="mb-0">

<i class="bi bi-person"></i>

Información del perfil

</h6>

</div>

<div class="card-body">

<form method="POST"
action="{{ route('profile.update') }}">

@csrf
@method('PATCH')

<x-form.input
name="name"
label="Nombre"
:value="old('name', $user->name)"
required
/>

<x-form.input
name="email"
type="email"
label="Correo electrónico"
:value="old('email', $user->email)"
required
/>

<x-form.button>

<i class="bi bi-save"></i>

Guardar cambios

</x-form.button>

</form>

</div>

</div>