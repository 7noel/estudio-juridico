@props([
    'name',
    'label' => null,
    'options' => [],
    'value' => null,
    'placeholder' => null
])

<div class="form-group">

    @if($label)
        <x-form.label :for="$name">
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