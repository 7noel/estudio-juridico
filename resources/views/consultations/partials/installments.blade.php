<div class="row mb-3">
    <div class="col-md-6">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0">Cuotas</h6>

            <x-form.button type="button" id="add-installment" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-plus"></i> Agregar
            </x-form.button>
        </div>

        <table class="table table-sm" id="installments-table">
            <thead>
                <tr>
                    <th style="width: 40%" class="text-center">Monto</th>
                    <th style="width: 25%" class="text-center">Fecha</th>
                    <th style="width: 5%"></th>
                </tr>
            </thead>
            <tbody>

                @if(isset($consultation))
                    @foreach($consultation->installments as $index => $inst)
                        <tr>
                            <td>
                                <input
                                    type="number" name="installments[{{ $index }}][amount]" class="form-control form-control-sm text-end" value="{{ $inst->amount }}" required>
                            </td>
                            <td>
                                <input type="date" name="installments[{{ $index }}][due_date]" class="form-control form-control-sm" value="{{ $inst->due_date }}">
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-danger btn-remove">X</button>
                            </td>
                        </tr>
                    @endforeach
                @endif

            </tbody>
        </table>
    </div>
</div>