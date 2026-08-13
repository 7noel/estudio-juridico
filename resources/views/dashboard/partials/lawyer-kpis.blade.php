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

{{-- KPIs de Conversión --}}

<div class="row mb-4">

    <div class="col-md-3">
        <div class="card border-info shadow-sm">
            <div class="card-body text-center">
                <h6>Mis Consultas</h6>
                <h2>{{ number_format($lawyerConsultations) }}</h2>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-success shadow-sm">
            <div class="card-body text-center">
                <h6>Convertidas a Caso</h6>
                <h2>{{ number_format($lawyerConvertedCases) }}</h2>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-primary shadow-sm">
            <div class="card-body text-center">
                <h6>Tasa de Conversión</h6>
                <h2>{{ number_format($lawyerConversionRate, 2) }}%</h2>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-warning shadow-sm">
            <div class="card-body text-center">
                <h6>En Seguimiento</h6>
                <h2>{{ number_format($lawyerProspects) }}</h2>
            </div>
        </div>
    </div>

</div>