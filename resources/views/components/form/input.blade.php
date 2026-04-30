@props([
    'name',
    'label' => null,
    'type' => 'text',
    'value' => '',
    'required' => false
])

<div class="form-group">

    @if($label)
        <x-form.label
            :for="$name"
            :required="$required"
        >
            {{ $label }}
        </x-form.label>
    @endif

    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ old($name, $value) }}"

        {{ $required ? 'required' : '' }}

        {{ $attributes->merge([
            'class' => 'form-control form-control-sm'
        ]) }}
    >

    <x-form.error :name="$name" />

</div>