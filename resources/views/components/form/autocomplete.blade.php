@props([

    'name',

    'label' => null,

    'value' => '',

    'text' => '',

    'required' => false,

    'createButton' => false,

    'createButtonId' => null,

])

<div {{ $attributes }}>

    @if($label)
        <div class="d-flex align-items-center gap-2 mb-2">

            @if($label)

                <label
                    for="{{ $name }}_search"
                    class="form-label mb-0">

                    {{ $label }}

                    @if($required)
                        <span class="text-danger">*</span>
                    @endif

                </label>

            @endif

            @if($createButton)

                <button
                    type="button"
                    class="btn btn-sm btn-outline-primary py-0 px-2"
                    id="{{ $createButtonId }}">

                    <i class="bi bi-plus"></i>

                </button>

            @endif

        </div>
    @endif

    <input
        type="text"
        id="{{ $name }}_search"
        class="form-control form-control-sm"
        value="{{ $text }}">

    <input
        type="hidden"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ $value }}">

</div>