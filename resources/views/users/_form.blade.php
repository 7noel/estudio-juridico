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
</div>
<div class="row mb-3">
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
        />
    </div>
</div>