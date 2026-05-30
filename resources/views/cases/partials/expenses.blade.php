<div class="card mt-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <strong> Gastos </strong>
        @if($canManageCaseContent)
            <button
                class="btn btn-sm btn-outline-primary"
                id="btnAddExpense"
                data-bs-toggle="modal"
                data-bs-target="#modalExpense"
            >
                <i class="bi bi-plus"></i> Agregar
            </button>
        @endif
    </div>
    <div class="card-body">
        <div id="expenses-list">
            @forelse($case->expenses as $expense)
                <div class="border rounded p-3 mb-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-danger">
                                    Gasto
                                </span>
                                <strong>
                                    {{ config('options.expense_categories')[$expense->category] ?? $expense->category }}
                                </strong>
                            </div>
                            <small class="text-muted">
                                {{ $expense->payment_method }}
                            </small>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold text-danger">
                                S/ {{ number_format($expense->amount, 2) }}
                            </div>
                            <small class="text-muted">
                                {{ $expense->expense_date?->format('d/m/Y') }}
                            </small>
                        </div>
                    </div>
                    @if($expense->description)
                        <div class="mt-2">
                            {{ $expense->description }}
                        </div>
                    @endif
                    <div class="mt-2 small text-muted">
                        Registrado por:
                        {{ $expense->user?->name }}
                    </div>
                    <div class="text-end mt-3 d-flex justify-content-end gap-2">
                        @if($expense->attachment)
                            <a href="{{ asset('storage/' . $expense->attachment) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-paperclip"></i> Ver archivo
                            </a>
                        @endif
                        @if($canManageCaseContent)
                            <button class="btn btn-sm btn-outline-success btn-edit-expense"
                                data-id="{{ $expense->id }}"
                                data-category="{{ $expense->category }}"
                                data-amount="{{ $expense->amount }}"
                                data-date="{{ $expense->expense_date?->format('Y-m-d') }}"
                                data-method="{{ $expense->payment_method }}"
                                data-reference="{{ $expense->reference }}"
                                data-description="{{ $expense->description }}"
                            >
                                <i class="bi bi-pencil"></i> Editar
                            </button>
                            <button class="btn btn-sm btn-outline-danger btn-delete-expense" data-id="{{ $expense->id }}">
                                <i class="bi bi-trash"></i> Eliminar
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-muted">No hay gastos registrados</div>
            @endforelse
        </div>
    </div>
</div>

{{-- MODAL --}}
<div class="modal fade" id="modalExpense" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="formExpense" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="case_id" value="{{ $case->id }}">
            <input type="hidden" name="expense_id" id="expense_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        Gasto
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label"> Categoría </label>
                            <select name="category" id="expense_category" class="form-select form-select-sm" required>
                                <option value=""> Seleccionar </option>
                                @foreach(config('options.expense_categories') as $key => $label)
                                    <option value="{{ $key }}">
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"> Monto </label>
                            <input type="number" step="0.01" name="amount" id="expense_amount" class="form-control form-control-sm" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label"> Fecha </label>
                            <input type="date" name="expense_date" id="expense_date" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"> Método de pago </label>
                            <select name="payment_method" id="expense_payment_method" class="form-select form-select-sm" required>
                                <option value=""> Seleccionar </option>
                                @foreach(config('options.payment_methods') as $key => $label)
                                    <option value="{{ $key }}"> {{ $label }} </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"> Referencia </label>
                            <input type="text" name="reference" id="expense_reference" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label"> Descripción </label>
                            <textarea name="description" id="expense_description" rows="3" class="form-control form-control-sm"></textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label"> Archivo </label>
                            <input type="file" name="attachment" class="form-control form-control-sm">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal"> Cancelar </button>
                    <button type="submit" class="btn btn-sm btn-outline-primary"> Guardar </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>

$(function(){

    // =============================
    // NUEVO
    // =============================

    $('#btnAddExpense').on('click', function(){

        $('#formExpense')[0].reset();

        $('#expense_id').val('');

    });

    // =============================
    // EDITAR
    // =============================

    $(document).on('click', '.btn-edit-expense', function(){

        $('#expense_id').val($(this).data('id'));

        $('#expense_category').val($(this).data('category'));

        $('#expense_amount').val($(this).data('amount'));

        $('#expense_date').val($(this).data('date'));

        $('#expense_payment_method').val($(this).data('method'));

        $('#expense_reference').val($(this).data('reference'));

        $('#expense_description').val($(this).data('description'));

        $('#modalExpense').modal('show');

    });

    // =============================
    // GUARDAR
    // =============================

    $('#formExpense').on('submit', function(e){

        e.preventDefault();

        const expenseId = $('#expense_id').val();

        const formData = new FormData(this);

        let url = '{{ route("expenses.store") }}';

        if(expenseId){

            url = `/expenses/${expenseId}`;

            formData.append('_method', 'PUT');
        }

        $.ajax({

            url,

            method: 'POST',

            data: formData,

            processData: false,

            contentType: false,

            success: function(){

                location.reload();
            },

            error: function(xhr){

                console.error(xhr.responseText);

                alert('Ocurrió un error');
            }

        });

    });

    // =============================
    // ELIMINAR
    // =============================

    $(document).on('click', '.btn-delete-expense', function(){

        if(!confirm('¿Eliminar gasto?')){
            return;
        }

        const id = $(this).data('id');

        $.ajax({

            url: `/expenses/${id}`,

            method: 'POST',

            data: {
                _method: 'DELETE',
                _token: '{{ csrf_token() }}'
            },

            success: function(){

                location.reload();
            },

            error: function(){

                alert('Ocurrió un error');
            }

        });

    });

});

</script>
@endpush