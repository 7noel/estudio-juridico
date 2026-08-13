<div class="card shadow-sm mb-4">

    <div class="card-body">

        <form method="GET" action="{{ route('dashboard') }}" class="row g-3 align-items-end">

            <div class="col-md-3">

                <label class="form-label">
                    Fecha Inicio
                </label>

                <input
                    type="date"
                    name="date_from"
                    class="form-control"
                    value="{{ request('date_from', now()->startOfMonth()->format('Y-m-d')) }}"
                >

            </div>

            <div class="col-md-3">

                <label class="form-label">
                    Fecha Fin
                </label>

                <input
                    type="date"
                    name="date_to"
                    class="form-control"
                    value="{{ request('date_to', now()->endOfMonth()->format('Y-m-d')) }}"
                >

            </div>

            <div class="col-md-3">

                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel"></i> Filtrar
                </button>

            </div>

            <div class="col-md-3">

                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-x-circle"></i> Limpiar
                </a>

            </div>

        </form>

    </div>

</div>