{{-- MODAL INGRESO --}}

<div class="modal fade" id="modalIncome" tabindex="-1">

    <div class="modal-dialog modal-lg">

        <form id="formIncome" autocomplete="off">

            @csrf

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title">

                        <i class="bi bi-plus-circle text-success"></i>

                        Registrar Ingreso

                    </h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

                </div>

                <div class="modal-body">

                    <div class="row g-3">

                        <div class="col-md-12">

                            <label class="form-label">

                                Concepto / Motivo <span class="text-danger">*</span>

                            </label>

                            <textarea
                                name="description"
                                id="income_description"
                                rows="2"
                                class="form-control form-control-sm"
                                placeholder="Ej.: Copias legalizadas"
                                required></textarea>

                        </div>

                        <div class="col-md-4">

                            <label class="form-label">

                                Monto (S/) <span class="text-danger">*</span>

                            </label>

                            <input
                                type="number"
                                step="0.01"
                                min="0.01"
                                name="amount"
                                id="income_amount"
                                class="form-control form-control-sm"
                                placeholder="0.00"
                                required>

                        </div>

                        <div class="col-md-4">

                            <label class="form-label">

                                Fecha de ingreso <span class="text-danger">*</span>

                            </label>

                            <input
                                type="date"
                                name="payment_date"
                                id="income_date"
                                class="form-control form-control-sm"
                                value="{{ now()->format('Y-m-d') }}"
                                required>

                        </div>

                        <div class="col-md-4">

                            <label class="form-label">

                                Método de pago <span class="text-danger">*</span>

                            </label>

                            <select
                                name="payment_method"
                                id="income_payment_method"
                                class="form-select form-select-sm"
                                required>

                                <option value="">Seleccionar</option>

                                @foreach(config('options.payment_methods') as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach

                            </select>

                        </div>

                        <div class="col-md-12">

                            <label class="form-label">

                                Referencia (opcional)

                            </label>

                            <input
                                type="text"
                                name="reference"
                                id="income_reference"
                                class="form-control form-control-sm"
                                placeholder="Ej.: Boleta 001-000123">

                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">

                        Cancelar

                    </button>

                    <button type="submit" class="btn btn-sm btn-success">

                        <i class="bi bi-check-lg"></i>

                        Guardar ingreso

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>

{{-- MODAL GASTO --}}

<div class="modal fade" id="modalCashExpense" tabindex="-1">

    <div class="modal-dialog modal-lg">

        <form id="formCashExpense" enctype="multipart/form-data" autocomplete="off">

            @csrf

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title">

                        <i class="bi bi-plus-circle text-danger"></i>

                        Registrar Gasto

                    </h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

                </div>

                <div class="modal-body">

                    <div class="row g-3">
                        <div class="col-md-4">

                            <label class="form-label">

                                Categoría <span class="text-danger">*</span>

                            </label>

                            <select
                                name="category"
                                id="cash_expense_category"
                                class="form-select form-select-sm"
                                required>

                                <option value="">Seleccionar</option>

                                @foreach(config('options.expense_categories') as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach

                            </select>

                        </div>

                        <div class="col-md-4">

                            <label class="form-label">

                                Monto (S/) <span class="text-danger">*</span>

                            </label>

                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                name="amount"
                                id="cash_expense_amount"
                                class="form-control form-control-sm"
                                placeholder="0.00"
                                required>

                        </div>

                        <div class="col-md-4">

                            <label class="form-label">

                                Fecha del gasto <span class="text-danger">*</span>

                            </label>

                            <input
                                type="date"
                                name="expense_date"
                                id="cash_expense_date"
                                class="form-control form-control-sm"
                                value="{{ now()->format('Y-m-d') }}"
                                required>

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">

                                Método de pago <span class="text-danger">*</span>

                            </label>

                            <select
                                name="payment_method"
                                id="cash_expense_payment_method"
                                class="form-select form-select-sm"
                                required>

                                <option value="">Seleccionar</option>

                                @foreach(config('options.payment_methods') as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach

                            </select>

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">

                                Referencia (opcional)

                            </label>

                            <input
                                type="text"
                                name="reference"
                                id="cash_expense_reference"
                                class="form-control form-control-sm"
                                placeholder="Ej.: Boleta 001-000456">

                        </div>

                        <div class="col-md-12">

                            <label class="form-label">

                                Descripción / Concepto <span class="text-danger">*</span>

                            </label>

                            <textarea
                                name="description"
                                id="cash_expense_description"
                                rows="2"
                                class="form-control form-control-sm"
                                placeholder="¿En qué se gastó?"
                                required></textarea>

                        </div>

                        <div class="col-md-12">

                            <label class="form-label">

                                Comprobante (opcional)

                            </label>

                            <input
                                type="file"
                                name="attachment"
                                id="cash_expense_attachment"
                                class="form-control form-control-sm"
                                accept=".jpg,.jpeg,.png,.pdf">

                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">

                        Cancelar

                    </button>

                    <button type="submit" class="btn btn-sm btn-danger">

                        <i class="bi bi-check-lg"></i>

                        Guardar gasto

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>

