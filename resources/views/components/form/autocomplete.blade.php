<div {{ $attributes }}>
	<label class="form-label">
		{{ $label }}
	</label>
	<input type="text" id="{{ $name }}_search" class="form-control form-control-sm" value="{{ $text }}">
	<input type="hidden" name="{{ $name }}" id="{{ $name }}" value="{{ $value }}">
</div>