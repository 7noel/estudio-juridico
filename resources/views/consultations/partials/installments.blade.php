<div class="row mb-3">

    {{-- CONFIGURACIÓN --}}
    <div class="col-md-9 col-xxl-6">
        <div class="card border-0 bg-light p-3">

            <div class="row align-items-end">

                <div class="col-md-2">
                    <x-form.input
                        name="total_amount"
                        label="Monto total"
                        type="number"
                        step="0.01"
                        id="total_amount"
                        :value="$consultation->total_amount ?? ''"
                    />
                </div>

                <div class="col-md-6">
                    <div class="form-check form-switch mt-4">
                        <input class="form-check-input" type="checkbox" id="auto_installments">
                        <label class="form-check-label" for="auto_installments">
                            Generar automáticamente
                        </label>
                    </div>
                </div>

                <div class="col-md-2">
                    <label>Cantidad</label>
                    <input type="number" id="installments_count" class="form-control form-control-sm" min="1">
                </div>

                <div class="col-md-2">
                    <button type="button" id="generate_installments" class="btn btn-sm btn-outline-primary w-100">
                        <i class="bi bi-gear"></i> Generar
                    </button>
                </div>

            </div>

        </div>
    </div>

</div>

{{-- TABLA --}}
<div class="row">
    <div class="col-md-9 col-xxl-6">

        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0">Cuotas</h6>

            <x-form.button type="button" id="add-installment" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-plus"></i> Agregar
            </x-form.button>
        </div>

        <table class="table table-sm table-bordered" id="installments-table">
            <thead>
                <tr>
                    <th class="text-center">Monto</th>
                    <th class="text-center">Fecha</th>
                    <th class="text-center">Estado</th>
                    <th style="width: 10%"></th>
                </tr>
            </thead>
            <tbody>

                @if(isset($consultation))
                    @foreach($consultation->installments as $index => $inst)
                        <tr>
                            <td>
                                <input type="number"
                                    name="installments[{{ $index }}][amount]"
                                    class="form-control form-control-sm text-end"
                                    value="{{ $inst->amount }}"
                                    required>
                            </td>
                            <td>
                                <input type="date"
                                    name="installments[{{ $index }}][due_date]"
                                    class="form-control form-control-sm"
                                    value="{{ $inst->due_date ? $inst->due_date->format('Y-m-d') : '' }}">
                            </td>
                            <td class="text-center">
                                @if($inst->is_paid)
                                    <span class="badge bg-success">Pagado</span>
                                @elseif($inst->paid_amount > 0)
                                    <span class="badge bg-warning text-dark">Parcial</span>
                                @else
                                    <span class="badge bg-secondary">Pendiente</span>
                                @endif
                            </td>   
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-danger btn-remove" title="Eliminar">X</button>
                            </td>
                        </tr>
                    @endforeach
                @endif

            </tbody>
        </table>

        <div class="mt-2 mb-3">
            <small>
                Total cuotas: <span id="sum_installments">0</span> |
                Diferencia: <span id="diff_installments">0</span>
            </small>
        </div>

    </div>
</div>

