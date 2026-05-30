<div class="row g-3 mb-4">

    <div class="col-md-2">

        <label>Fecha inicio</label>

        <input
            type="date"
            id="date_from"
            class="form-control"
            value="{{ now()->startOfMonth()->format('Y-m-d') }}"
        >

    </div>

    <div class="col-md-2">

        <label>Fecha fin</label>

        <input
            type="date"
            id="date_to"
            class="form-control"
            value="{{ now()->endOfMonth()->format('Y-m-d') }}"
        >

    </div>

    <div class="col-md-2">

        <label>Sede</label>

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

    <div class="col-md-2">

        <label>Especialidad</label>

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

        <label>Abogado</label>

        <select
            id="lawyer_id"
            class="form-select"
        >

            <option value="">
                Todos
            </option>

            @foreach($lawyers as $item)

                <option value="{{ $item->id }}">
                    {{ $item->name }}
                </option>

            @endforeach

        </select>

    </div>

    <div class="col-md-2">

        <label>Servicio</label>

        <select
            id="service_type"
            class="form-select"
        >

            <option value="">
                Todos
            </option>

            @foreach(config('options.service_types') as $key => $value)

                <option value="{{ $key }}">
                    {{ $value }}
                </option>

            @endforeach

        </select>

    </div>

</div>

<div class="row mb-4">

    <div class="col-md-3">

        <label>Estado</label>

        <select
            id="status"
            class="form-select"
        >

            <option value="">
                Todos
            </option>

            <option value="open">
                Abiertos
            </option>

            <option value="in_progress">
                En Proceso
            </option>

            <option value="paused">
                Pausados
            </option>

            <option value="closed">
                Cerrados
            </option>

        </select>

    </div>

    <div class="col-md-2 d-flex align-items-end">

        <button
            class="btn btn-primary w-100"
            id="btn-search"
        >

            Buscar

        </button>

    </div>

</div>