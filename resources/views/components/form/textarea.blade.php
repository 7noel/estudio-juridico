@props([
    'name',
    'label' => null,
    'rows' => 3
])

<div class="form-group">

    @if($label)
        <x-form.label :for="$name">
            {{ $label }}
        </x-form.label>
    @endif

    <textarea
        name="{{ $name }}"
        id="{{ $name }}"
        rows="{{ $rows }}"

        {{ $attributes->merge([
            'class' => 'form-control form-control-sm'
        ]) }}
    >{{ old($name) }}</textarea>

    <x-form.error :name="$name" />

</div>