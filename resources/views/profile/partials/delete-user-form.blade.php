<div class="card border-danger shadow-sm">

<div class="card-header bg-danger text-white">

<h6 class="mb-0">

<i class="bi bi-trash"></i>

Eliminar cuenta

</h6>

</div>

<div class="card-body">

<p class="text-muted">

Una vez eliminada la cuenta,
todos los datos serán eliminados
permanentemente.

</p>

<form method="POST"
action="{{ route('profile.destroy') }}">

@csrf
@method('DELETE')

<x-form.input
name="password"
type="password"
label="Confirme su contraseña"
required
/>

<x-form.button
class="btn-outline-danger">

<i class="bi bi-trash"></i>

Eliminar cuenta

</x-form.button>

</form>

</div>

</div>