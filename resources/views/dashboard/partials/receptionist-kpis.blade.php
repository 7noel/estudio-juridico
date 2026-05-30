<div class="row mb-4">

    <div class="col-md-3">
        <div class="card border-primary shadow-sm">
            <div class="card-body text-center">
                <h6>Consultas Pendientes</h6>
                <h2>{{ number_format($pendingConsultations) }}</h2>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-success shadow-sm">
            <div class="card-body text-center">
                <h6>Eventos Hoy</h6>
                <h2>{{ number_format($todayEvents) }}</h2>
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
                <h6>Cobranza Pendiente</h6>
                <h2>S/ {{ number_format($pendingPayments,2) }}</h2>
            </div>
        </div>
    </div>

</div>