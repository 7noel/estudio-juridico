@props([
    'name',
    'label' => null,
    'options' => [],
    'value' => null,
    'selected' => null,
    'placeholder' => null,
    'required' => false
])

@php
$value = $selected ?? $value;
@endphp

<div class="form-group">

    @if($label)
        <x-form.label :for="$name" :required="$required">
            {{ $label }}
        </x-form.label>
    @endif

    <select
        name="{{ $name }}"
        id="{{ $name }}"

        {{ $attributes->merge([
            'class' => 'form-control form-control-sm'
        ]) }}
    >

        @if($placeholder)
            <option value="">
                {{ $placeholder }}
            </option>
        @endif

        @foreach($options as $key => $text)

            <option
                value="{{ $key }}"
                {{ old($name, $value) == $key ? 'selected' : '' }}
            >
                {{ $text }}
            </option>

        @endforeach

    </select>

    <x-form.error :name="$name" />

</div>