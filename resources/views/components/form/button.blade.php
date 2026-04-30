@props([
    'type' => 'submit'
])

<button
    type="{{ $type }}"

    {{ $attributes->merge([
        'class' => 'btn btn-outline-primary btn-sm'
    ]) }}
>

{{ $slot }}

</button>