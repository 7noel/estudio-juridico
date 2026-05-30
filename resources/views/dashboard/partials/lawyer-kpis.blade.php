<div class="row mb-4">

    <div class="col-md-3">
        <div class="card border-primary shadow-sm">
            <div class="card-body text-center">
                <h6>Mis Casos Activos</h6>
                <h2>{{ number_format($casesInProgress) }}</h2>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-success shadow-sm">
            <div class="card-body text-center">
                <h6>Consultas Pendientes</h6>
                <h2>{{ number_format($pendingConsultations) }}</h2>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-warning shadow-sm">
            <div class="card-body text-center">
                <h6>Vencimientos Próximos</h6>
                <h2>{{ number_format($upcomingDeadlines) }}</h2>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-danger shadow-sm">
            <div class="card-body text-center">
                <h6>Sin Actividad</h6>
                <h2>{{ number_format($inactiveCases) }}</h2>
            </div>
        </div>
    </div>

</div>