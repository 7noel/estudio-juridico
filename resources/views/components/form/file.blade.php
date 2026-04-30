@props([
    'name',
    'label' => null,
    'accept' => null,
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
        type="file"
        name="{{ $name }}"
        id="{{ $name }}"

        {{ $required ? 'required' : '' }}

        @if($accept)
            accept="{{ $accept }}"
        @endif

        {{ $attributes->merge([
            'class' => 'form-control-file'
        ]) }}
    >

    <x-form.error :name="$name" />

</div>