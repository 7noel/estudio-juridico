@props([
    'name',
    'label' => null,
    'value' => '',
    'required' => false
])
<div {{ $attributes }}>
    @if($label)
        <x-form.label
            :for="$name"
            :required="$required"
        >
            {{ $label }}
        </x-form.label>
    @endif
	<input type="text" id="{{ $name }}_search" class="form-control form-control-sm" value="{{ $text }}">
	<input type="hidden" name="{{ $name }}" id="{{ $name }}" value="{{ $value }}">
</div>