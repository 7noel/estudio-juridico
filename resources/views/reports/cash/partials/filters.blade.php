<div class="row mb-4">

    <div class="col-md-3">

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

    <div class="col-md-3">

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

    <div class="col-md-3">

        <label>

            Sede

        </label>

        <select
            id="establishment_id"
            class="form-control"
        >

            <option value="">

                Todas

            </option>

            @foreach($establishments as $establishment)

                <option
                    value="{{ $establishment->id }}"
                >

                    {{ $establishment->name }}

                </option>

            @endforeach

        </select>

    </div>

    <div class="col-md-3 d-flex align-items-end">

        <button
            id="btn-search"
            class="btn btn-primary w-100"
        >

            Buscar

        </button>

    </div>

</div>

<div class="mb-4">

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