<div class="row mb-3">
    <div class="col-md-3">
        <x-form.input
        name="name"
        label="Nombre"
        :value="$user->name ?? ''"
        required
        />
    </div>
    <div class="col-md-3">
        <x-form.select
        name="role"
        label="Rol"
        :options="$roles"
        :value="$user->getRoleNames()->first() ?? ''"
        required
        />
    </div>
    <div class="col-md-3">
        <x-form.select
        name="establishment_id"
        label="Establecimiento"
        :options="$establishments"
        :selected="$user->establishment_id ?? '1'"
        required
        />
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-3">
        <x-form.input
        name="mobile"
        label="Celular"
        type="tel"
        inputmode="tel"
        maxlength="16"
        placeholder="999 123 456 · +34 612 345 678"
        :value="$user->mobile ?? ''"
        />
        <div class="form-text text-muted small">
            Perú: 9 dígitos (ej. 999 123 456). Extranjero: +código de país + número (ej. +34 612 345 678).
        </div>
    </div>
    <div class="col-md-3">
        <x-form.input
        name="email"
        label="Email"
        type="email"
        :value="$user->email ?? ''"
        required
        />
    </div>
    <div class="col-md-3">
        <x-form.input
        name="password"
        label="Contraseña"
        type="password"
        togglePassword="true"
        />
    </div>
</div>

@push('scripts')

<script>
$(document).on('input', '[name="mobile"]', function(){

    // Solo dígitos y el signo + (espacios, guiones y paréntesis se eliminan solos)
    this.value = this.value.replace(/[^\d+]/g, '');

});
</script>

@endpush