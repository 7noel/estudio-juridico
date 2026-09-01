<div class="row g-3 mb-4">

    <div class="col-md-3">

        <label>

            Abogado

        </label>

        <select
            id="lawyer_id"
            class="form-select"
        >

            <option value="">

                Todos

            </option>

            @foreach($lawyers as $lawyer)

                <option value="{{ $lawyer->id }}">

                    {{ $lawyer->name }}

                </option>

            @endforeach

        </select>

    </div>

    <div class="col-md-3">

        <label>

            Sede

        </label>

        <select
            id="establishment_id"
            class="form-select"
        >

            <option value="">

                Todas

            </option>

            @foreach($establishments as $establishment)

                <option value="{{ $establishment->id }}">

                    {{ $establishment->name }}

                </option>

            @endforeach

        </select>

    </div>

    <div class="col-md-2">

        <label>

            Fecha inicio

        </label>

        <input
            type="date"
            id="date_start"
            class="form-control"
            value="{{ now()->format('Y-m-d') }}"
        >

    </div>

    <div class="col-md-2">

        <label>

            Fecha fin

        </label>

        <input
            type="date"
            id="date_end"
            class="form-control"
            value="{{ now()->format('Y-m-d') }}"
        >

    </div>

    <div class="col-md-2 d-flex align-items-end">

        <button
            id="btn-search"
            class="btn btn-primary w-100"
        >

            Buscar

        </button>

    </div>

</div>

<div class="row mb-3">

    <div class="col-md-4 d-flex align-items-end">

        <div class="btn-group">

            <button
                class="btn btn-outline-secondary quick-range"
                data-range="today"
            >
                Hoy
            </button>

            <button
                class="btn btn-outline-secondary quick-range"
                data-range="yesterday"
            >
                Ayer
            </button>

            <button
                class="btn btn-outline-secondary quick-range"
                data-range="week"
            >
                Semana
            </button>

            <button
                class="btn btn-outline-secondary quick-range"
                data-range="month"
            >
                Mes
            </button>

        </div>

    </div>

</div>

<div class="mb-4">

    <label class="form-label fw-semibold d-block">

        Tipos de actividad

    </label>

    <div class="d-flex flex-wrap gap-3">

        @foreach($activityTypes as $key => $label)

            <div class="form-check">

                <input
                    class="form-check-input activity-type"
                    type="checkbox"
                    value="{{ $key }}"
                    id="type_{{ $key }}"
                    checked
                >

                <label
                    class="form-check-label"
                    for="type_{{ $key }}"
                >

                    {{ $label }}

                </label>

            </div>

        @endforeach

        <button
            type="button"
            class="btn btn-sm btn-link p-0 align-self-center"
            id="btn-toggle-types"
        >

            Quitar todas

        </button>

    </div>

</div>
