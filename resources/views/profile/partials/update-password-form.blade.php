<div class="card border-0 shadow-sm">

	<div class="card-header bg-white">
		<h6 class="mb-0">
			<i class="bi bi-key"></i> Actualizar contraseña
		</h6>
	</div>

	<div class="card-body">
		<form method="POST" action="{{ route('password.update') }}">
			@csrf
			@method('PUT')
			<x-form.input name="current_password" type="password" label="Contraseña actual" required/>
			<x-form.input name="password" type="password" label="Nueva contraseña" required />
			<x-form.input name="password_confirmation" type="password" label="Confirmar contraseña" required />
			<x-form.button class="btn-outline-warning mt-3">
				<i class="bi bi-shield-lock"></i> Actualizar contraseña
			</x-form.button>
		</form>
	</div>
</div>