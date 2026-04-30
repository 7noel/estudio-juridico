@props([
    'name',
    'label' => null,
    'checked' => false
])

<div class="form-group form-check">

    <input
        type="checkbox"
        name="{{ $name }}"
        id="{{ $name }}"
        value="1"

        {{ old($name, $checked) ? 'checked' : '' }}

        {{ $attributes->merge([
            'class' => 'form-check-input'
        ]) }}
    >

    @if($label)
        <label
            for="{{ $name }}"
            class="form-check-label"
        >
            {{ $label }}
        </label>
    @endif

</div>