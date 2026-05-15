<div class="mb-3">
	<label>
		Nombre del rol
	</label>
	<input
	type="text"
	name="name"
	class="form-control form-control-sm"
	value="{{ $role->name ?? '' }}"
	required>
</div>
<div class="mb-3">
	<label>
		Permisos
	</label>
	<div class="row">
		@foreach($permissions as $permission)
		<div class="col-md-3">
			<label>
				<input
				type="checkbox"
				name="permissions[]"
				value="{{ $permission }}"
				@if(isset($rolePermissions) && in_array($permission, $rolePermissions))
					checked
				@endif
				>
			{{ $permission }}
			</label>
		</div>
		@endforeach
	</div>
</div>