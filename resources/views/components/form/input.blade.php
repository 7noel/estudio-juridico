@props([
    'name',
    'label' => null,
    'type' => 'text',
    'value' => '',
    'required' => false,
    'uppercase' => false,
    'togglePassword' => false
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

    @if($togglePassword && $type === 'password')

        <div class="input-group">

            <input
                type="password"
                name="{{ $name }}"
                id="{{ $name }}"
                value="{{ old($name, $value) }}"

                {{ $required ? 'required' : '' }}

                {{ $attributes->merge([
                    'class' => 'form-control form-control-sm password-input ' . ($uppercase ? 'text-uppercase' : '')
                ]) }}
            >

            <button
                class="btn btn-sm btn-outline-secondary toggle-password"
                type="button"
            >
                <i class="bi bi-eye"></i>
            </button>

        </div>

    @else

        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $name }}"
            value="{{ old($name, $value) }}"

            {{ $required ? 'required' : '' }}

            {{ $attributes->merge([
                'class' => 'form-control form-control-sm ' . ($uppercase ? 'text-uppercase' : '')
            ]) }}
        >

    @endif

    <x-form.error :name="$name" />

</div>