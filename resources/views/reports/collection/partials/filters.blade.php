<div class="row g-3 mb-4">

    <div class="col-md-2 col-sm-6">

        <label class="form-label">
            Fecha inicio
        </label>

        <input
            type="date"
            id="date_from"
            class="form-control"
            value="{{ now()->startOfMonth()->format('Y-m-d') }}"
        >

    </div>

    <div class="col-md-2 col-sm-6">

        <label class="form-label">
            Fecha fin
        </label>

        <input
            type="date"
            id="date_to"
            class="form-control"
            value="{{ now()->endOfMonth()->format('Y-m-d') }}"
        >

    </div>

    <div class="col-md-2 col-sm-6">

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

    <div class="col-md-2 col-sm-6">

        <label class="form-label">
            Abogado
        </label>

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

    <div class="col-md-2 col-sm-6">

        <label class="form-label">
            Estado
        </label>

        <select
            id="status"
            class="form-select"
        >

            <option value="">
                Todos
            </option>

            <option value="paid">
                Pagados
            </option>

            <option value="pending">
                Pendientes
            </option>

            <option value="overdue">
                Vencidos
            </option>

        </select>

    </div>

    <div class="col-md-2 col-sm-6 d-flex align-items-end">

        <button
            class="btn btn-primary w-100"
            id="btn-search"
        >
            Buscar
        </button>

    </div>

</div>

<div class="row mb-4">

    <div class="col-md-12">

        <div class="form-check">

            <input
                class="form-check-input"
                type="checkbox"
                id="include_overdue"
            >

            <label
                class="form-check-label"
                for="include_overdue"
            >

                Incluir cuotas vencidas anteriores

            </label>

        </div>

    </div>

</div>