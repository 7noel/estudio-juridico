<div class="row mb-4">

    <div class="col-md-3">
        <div class="card border-success shadow-sm">
            <div class="card-body text-center">
                <h6>Ingresos del Mes</h6>
                <h2>S/ {{ number_format($monthlyIncome,2) }}</h2>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-danger shadow-sm">
            <div class="card-body text-center">
                <h6>Gastos del Mes</h6>
                <h2>S/ {{ number_format($monthlyExpense,2) }}</h2>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-primary shadow-sm">
            <div class="card-body text-center">
                <h6>Utilidad</h6>
                <h2>S/ {{ number_format($monthlyProfit,2) }}</h2>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-warning shadow-sm">
            <div class="card-body text-center">
                <h6>Cobranza Pendiente</h6>
                <h2>S/ {{ number_format($pendingPayments,2) }}</h2>
            </div>
        </div>
    </div>

</div>