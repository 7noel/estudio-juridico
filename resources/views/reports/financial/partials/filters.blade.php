<div class="row g-3 mb-4">

    <div class="col-md-2">

        <label class="form-label">
            Fecha Inicio
        </label>

        <input
            type="date"
            class="form-control"
            id="date_from"
            value="{{ now()->startOfMonth()->format('Y-m-d') }}"
        >

    </div>

    <div class="col-md-2">

        <label class="form-label">
            Fecha Fin
        </label>

        <input
            type="date"
            class="form-control"
            id="date_to"
            value="{{ now()->endOfMonth()->format('Y-m-d') }}"
        >

    </div>

    <div class="col-md-2">

        <label class="form-label">
            Sede
        </label>

        <select
            id="establishment_id"
            class="form-select"
        >

            <option value="">
                Todas
            </option>

            @foreach($establishments as $item)

                <option value="{{ $item->id }}">
                    {{ $item->name }}
                </option>

            @endforeach

        </select>

    </div>

    <div class="col-md-3">

        <label class="form-label">
            Especialidad
        </label>

        <select
            id="specialty_id"
            class="form-select"
        >

            <option value="">
                Todas
            </option>

            @foreach($specialties as $item)

                <option value="{{ $item->id }}">
                    {{ $item->name }}
                </option>

            @endforeach

        </select>

    </div>

    <div class="col-md-2">

        <label class="form-label">
            Servicio
        </label>

        <select
            id="service_type"
            class="form-select"
        >

            <option value="">
                Todos
            </option>

            @foreach(config('options.service_types') as $key => $label)

                <option value="{{ $key }}">
                    {{ $label }}
                </option>

            @endforeach

        </select>

    </div>

    <div class="col-md-1 d-flex align-items-end">

        <button
            class="btn btn-primary w-100"
            id="btn-search"
        >

            Buscar

        </button>

    </div>

</div>