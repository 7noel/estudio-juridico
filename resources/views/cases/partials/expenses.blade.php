<div class="card mt-3">
    <div class="card-body border-bottom">
        @php

            $expenses = $case->expenses;

            $counts = [];

            foreach(config('options.expense_categories') as $key => $label){

                $counts[$key] = $expenses
                    ->where('category', $key)
                    ->count();

            }

        @endphp

        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3">

            <div class="flex-grow-1">

                <label class="form-label fw-semibold mb-2">

                    Filtrar gastos

                </label>

                <div
                    class="d-flex flex-wrap gap-2"
                    id="expenseFilters">

                    <button
                        class="btn btn-sm btn-primary active"
                        data-filter="all">

                        Todos

                        <span class="badge bg-light text-dark ms-1">

                            {{ $expenses->count() }}

                        </span>

                    </button>

                    @foreach(config('options.expense_categories') as $key => $label)

                        <button
                            class="btn btn-sm btn-outline-secondary"
                            data-filter="{{ $key }}">

                            {{ $label }}

                            <span class="badge bg-secondary ms-1">

                                {{ $counts[$key] }}

                            </span>

                        </button>

                    @endforeach

                </div>

            </div>

            @if($canManageCaseContent)

                <div class="flex-shrink-0">

                    <button
                        class="btn btn-sm btn-outline-primary"
                        id="btnAddExpense"
                        data-bs-toggle="modal"
                        data-bs-target="#modalExpense">

                        <i class="bi bi-plus"></i>

                        Agregar gasto

                    </button>

                </div>

            @endif

        </div>

        <div class="table-responsive mt-3">

            <table
                class="table table-sm table-hover align-middle mb-0"
                id="expensesTable">

                <thead class="table-light">

                    <tr>

                        <th style="width:120px">
                            Fecha
                        </th>

                        <th style="width:180px">
                            Categoría
                        </th>

                        <th>
                            Descripción
                        </th>

                        <th style="width:150px">
                            Monto
                        </th>

                        <th style="width:120px">
                            Archivo
                        </th>

                        @if($canManageCaseContent)

                            <th
                                class="text-end"
                                style="width:200px">

                                Acciones

                            </th>

                        @endif

                    </tr>

                </thead>

                <tbody>
                    @forelse($expenses as $expense)

                    <tr
                        class="expense-item"
                        data-category="{{ $expense->category }}">

                        <td data-label="Fecha">

                            {{ $expense->expense_date?->format('d/m/Y') }}

                        </td>

                        <td data-label="Categoría">

                            {{ config('options.expense_categories')[$expense->category] ?? $expense->category }}

                        </td>

                        <td data-label="Descripción">

                            @php

                                $description = trim($expense->description ?? '');

                            @endphp

                            @if($description)

                                @if(strlen($description) > 120)

                                    <div class="expense-description-short">

                                        {{ \Illuminate\Support\Str::limit($description,120) }}

                                    </div>

                                    <div
                                        class="expense-description-full"
                                        style="display:none;">

                                        {{ $description }}

                                    </div>

                                    <a
                                        href="#"
                                        class="expense-toggle small">

                                        Ver más

                                    </a>

                                @else

                                    {{ $description }}

                                @endif

                            @else

                                <span class="text-muted">

                                    —

                                </span>

                            @endif

                        </td>

                        <td data-label="Monto">

                            <div class="fw-semibold text-danger">

                                S/ {{ number_format($expense->amount,2) }}

                            </div>

                            @if($expense->reference)

                                <small class="text-muted">

                                    Ref.: {{ $expense->reference }}

                                </small>

                            @endif

                        </td>

                        <td data-label="Archivo">

                            @if($expense->attachment)

                                <a
                                    href="{{ asset('storage/'.$expense->attachment) }}"
                                    target="_blank">

                                    Ver archivo

                                </a>

                            @else

                                <span class="text-muted">

                                    —

                                </span>

                            @endif

                        </td>

                        @if($canManageCaseContent)

                        <td
                            data-label="Acciones"
                            class="text-end">

                            <div class="d-flex justify-content-end gap-2">

                                <button
                                    class="btn btn-sm btn-outline-primary btn-edit-expense"

                                    data-id="{{ $expense->id }}"
                                    data-category="{{ $expense->category }}"
                                    data-amount="{{ $expense->amount }}"
                                    data-date="{{ $expense->expense_date?->format('Y-m-d') }}"
                                    data-method="{{ $expense->payment_method }}"
                                    data-reference="{{ $expense->reference }}"
                                    data-description="{{ $expense->description }}">

                                    <i class="bi bi-pencil"></i>

                                    Editar

                                </button>

                                <button
                                    class="btn btn-sm btn-outline-danger btn-delete-expense"

                                    data-id="{{ $expense->id }}">

                                    <i class="bi bi-trash"></i>

                                    Eliminar

                                </button>

                            </div>

                        </td>

                        @endif

                    </tr>

                    @empty

                    <tr>

                        <td
                            colspan="{{ $canManageCaseContent ? 6 : 5 }}"
                            class="text-center py-5">

                            No hay gastos registrados.

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

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

// ==========================================
// FILTRAR GASTOS
// ==========================================

$(document).on(
    'click',
    '#expenseFilters .btn',
    function(){

        $('#expenseFilters .btn')
            .removeClass('btn-primary active')
            .addClass('btn-outline-secondary');

        $(this)
            .removeClass('btn-outline-secondary')
            .addClass('btn-primary active');

        const filter = $(this).data('filter');

        if(filter === 'all'){

            $('.expense-item').show();

            return;

        }

        $('.expense-item').hide();

        $('.expense-item[data-category="'+filter+'"]').show();

    }
);

// ==========================================
// VER MÁS / VER MENOS
// ==========================================

$(document).on(
    'click',
    '.expense-toggle',
    function(e){

        e.preventDefault();

        const row = $(this).closest('td');

        row.find('.expense-description-short').slideToggle(150);

        row.find('.expense-description-full').slideToggle(150);

        $(this).text(

            $(this).text() === 'Ver más'

                ? 'Ver menos'

                : 'Ver más'

        );

    }
);

</script>
@endpush

@push('styles')

<style>

#expensesTable td,
#expensesTable th{
    vertical-align:middle;
}

.expense-toggle{
    text-decoration:none;
}

@media(max-width:768px){

    #expensesTable thead{
        display:none;
    }

    #expensesTable,
    #expensesTable tbody,
    #expensesTable tr,
    #expensesTable td{

        display:block;
        width:100%;

    }

    #expensesTable tr{

        margin-bottom:1rem;
        border:1px solid #dee2e6;
        border-radius:.6rem;
        overflow:hidden;
        background:#fff;
        box-shadow:0 .125rem .25rem rgba(0,0,0,.05);

    }

    #expensesTable td{

        border:none;
        border-bottom:1px solid #f1f1f1;
        padding:.75rem 1rem;
        text-align:left !important;

    }

    #expensesTable td:last-child{

        border-bottom:none;

    }

    #expensesTable td::before{

        content:attr(data-label);

        display:block;

        font-size:.72rem;
        font-weight:600;

        color:#6c757d;

        text-transform:uppercase;

        margin-bottom:.25rem;

    }

    #expensesTable .btn{

        flex:1;

    }

}

</style>

@endpush