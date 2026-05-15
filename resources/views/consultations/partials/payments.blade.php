<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    Gestión de pagos - Cuota #<span id="modal_installment_number"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                {{-- RESUMEN --}}
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <small class="text-muted">Monto cuota</small><br>
                            <strong id="modal_amount"></strong>
                        </div>
                        <div>
                            <small class="text-muted">Pagado</small><br>
                            <strong class="text-success" id="modal_paid"></strong>
                        </div>
                        <div>
                            <small class="text-muted">Saldo</small><br>
                            <strong class="text-danger" id="modal_pending"></strong>
                        </div>
                    </div>
                </div>

                <hr>

                {{-- TIMELINE --}}
                <div class="mb-3">
                    <h6>Historial de pagos</h6>

                    <div id="payments_list" style="max-height:200px; overflow:auto;">
                        {{-- dinámico --}}
                    </div>
                </div>

                <hr>

                {{-- FORM --}}
                <form id="paymentForm">
                    @csrf

                    <input type="hidden" name="installment_id" id="installment_id">
                    <input type="hidden" name="consultation_id" id="consultation_id">

                    <div class="row">

                        <div class="col-md-3">
                            <label>Monto</label>
                            <input type="number" step="0.01" name="amount" id="payment_amount" class="form-control form-control-sm text-end" required>
                        </div>

                        <div class="col-md-3">
                            <label>Fecha</label>
                            <input type="date" name="payment_date" id="payment_date" class="form-control form-control-sm" required>
                        </div>

                        <div class="col-md-3">
                            <label>Medio</label>
                            <select name="payment_method" class="form-control form-control-sm">
                                @foreach(config('options.payment_methods') as $k => $v)
                                    <option value="{{ $k }}">{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label>Referencia</label>
                            <input type="text" name="reference" class="form-control form-control-sm">
                        </div>

                    </div>

                    {{-- SWITCH CASO --}}
                    <div class="mt-2" id="generate_case_wrapper">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="generate_case" id="generate_case">
                            <label class="form-check-label">
                                Generar caso
                            </label>
                        </div>
                    </div>

                    <div class="mt-3 text-end">
                        <button class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-save"></i> Guardar pago
                        </button>
                    </div>

                </form>

            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>

$(document).on('click', '.btn-pay', function(){

    let installmentId = $(this).data('id');
    let consultationId = $(this).data('consultation');

    $('#installment_id').val(installmentId);
    $('#consultation_id').val(consultationId);

    // fecha hoy
    let today = new Date().toISOString().split('T')[0];
    $('#payment_date').val(today);

    // cargar data
    $.get("{{ route('payments.data') }}", { installment_id: installmentId }, function(res){

        $('#modal_installment_number').text(res.installment.installment_number);
        $('#modal_amount').text('S/ ' + res.installment.amount);
        $('#modal_paid').text('S/ ' + res.installment.paid);
        $('#modal_pending').text('S/ ' + res.installment.pending);

        $('#payment_amount').val(res.installment.pending);

        // timeline
        let html = '';

        if(res.payments.length === 0){
            html = '<small class="text-muted">Sin pagos registrados</small>';
        }

        res.payments.forEach(p => {

            html += `
                <div class="border rounded p-2 mb-1 d-flex justify-content-between align-items-center">
                    <div>
                        <strong>S/ ${p.amount}</strong> - 
                        <small>${p.date} - ${p.method} ${p.reference}</small>
                    </div>
                    <div>
                        <button class="btn btn-sm btn-outline-danger btn-delete-payment" data-id="${p.id}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            `;
        });

        $('#payments_list').html(html);

        // mostrar switch solo si NO hay caso
        if(res.has_case){
            $('#generate_case_wrapper').hide();
        }else{
            $('#generate_case_wrapper').show();
            $('#generate_case').prop('checked', true);
        }

        if(res.installment.is_paid){
            $('#paymentForm').hide(); // 🔥 oculta formulario
        }else{
            $('#paymentForm').show(); // 🔥 muestra formulario
        }

        if(res.installment.is_paid){
            $('.modal-title').text('Historial de pagos');
        }else{
            $('.modal-title').text('Registrar pago');
        }

        $('#paymentModal').modal('show');

    });

});


// guardar pago
$('#paymentForm').submit(function(e){

    e.preventDefault();

    $.post("{{ route('payments.store') }}", $(this).serialize(), function(){

        $('#paymentModal').modal('hide');
        location.reload();

    }).fail(function(xhr){

        alert(xhr.responseJSON?.error || 'Error');

    });

});


// eliminar pago
$(document).on('click', '.btn-delete-payment', function(){

    let id = $(this).data('id');

    if(confirm('Eliminar pago?')){

        $.post("{{ route('payments.delete') }}", {
            _token: '{{ csrf_token() }}',
            id: id
        }, function(){
            location.reload();
        });

    }

});

$(document).on('click', '.btn-mark-evaluated', function(){

    $.post("{{ route('consultations.change-status', $consultation->id) }}", {
        _token: '{{ csrf_token() }}',
        status: 'evaluated'
    }, function(){
        location.reload();
    });

});

// $(document).on('click', '.btn-reject', function(){
//     if(!confirm('¿Desea rechazar la consulta?')) return;
//     $.post("{{ route('consultations.reject', $consultation->id) }}", {
//         _token: '{{ csrf_token() }}'
//     }, function(){
//         location.reload();
//     });
// });


let actionUrl = null;

$(document).on('click', '.btn-generate-case', function(){

    let id = $(this).data('id');

    actionUrl = "{{ route('consultations.generate-case', ':id') }}".replace(':id', id);

    $('#confirmMessage').text('¿Desea generar el caso?');
    $('#confirmModal').modal('show');
    $('#confirmActionBtn').removeClass('btn-primary btn-danger').addClass('btn-success');

});


$(document).on('click', '.btn-reject', function(){

    let id = $(this).data('id');

    actionUrl = "{{ route('consultations.reject', ':id') }}".replace(':id', id);

    $('#confirmMessage').text('¿Desea rechazar la consulta?');
    $('#confirmModal').modal('show');
    $('#confirmActionBtn').removeClass('btn-primary btn-danger') .addClass('btn-danger');

});


$('#confirmActionBtn').click(function(){

    if(!actionUrl) return;

    $.post(actionUrl, {
        _token: '{{ csrf_token() }}'
    }, function(){
        location.reload();
    });

});

</script>
@endpush