@props([
    'name',
    'label' => null,
    'value' => null,
    'checked' => false,
])

<div class="form-check">

    <input
        type="radio"
        name="{{ $name }}"
        id="{{ $name }}_{{ $value }}"
        value="{{ $value }}"

        {{ old($name, $checked) == $value ? 'checked' : '' }}

        {{ $attributes->merge([
            'class' => 'form-check-input'
        ]) }}
    >

    @if($label)
        <label
            for="{{ $name }}_{{ $value }}"
            class="form-check-label"
        >
            {{ $label }}
        </label>
    @endif

</div>