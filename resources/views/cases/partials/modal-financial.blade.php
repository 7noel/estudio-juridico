<div class="modal fade" id="financialStatusModal" tabindex="-1">

    <div class="modal-dialog modal-lg">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">

                    Estado financiero

                </h5>

                <button
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body">

                @php($consultation = $case->consultation)

                @if($consultation)

                    <div class="row text-center mb-4">

                        <div class="col-md-4">

                            <small>Total</small>

                            <h5>

                                S/ {{ number_format($consultation->total_amount,2) }}

                            </h5>

                        </div>

                        <div class="col-md-4">

                            <small>Pagado</small>

                            <h5 class="text-success">

                                S/ {{ number_format($consultation->paid_amount,2) }}

                            </h5>

                        </div>

                        <div class="col-md-4">

                            <small>Pendiente</small>

                            <h5 class="text-danger">

                                S/ {{ number_format($consultation->pending_amount,2) }}

                            </h5>

                        </div>

                    </div>

                    <table class="table table-bordered table-sm">

                        <thead>

                            <tr>

                                <th>#</th>

                                <th>Monto</th>

                                <th>Pagado</th>

                                <th>Saldo</th>

                                <th>Vence</th>

                                <th>Estado</th>

                            </tr>

                        </thead>

                        <tbody>

                            @foreach($consultation->installments as $installment)

                                <tr>

                                    <td>

                                        {{ $installment->installment_number }}

                                    </td>

                                    <td>

                                        {{ number_format($installment->amount,2) }}

                                    </td>

                                    <td>

                                        {{ number_format($installment->paid_amount,2) }}

                                    </td>

                                    <td>

                                        {{ number_format($installment->pending_amount,2) }}

                                    </td>

                                    <td>

                                        {{ $installment->due_date->format('d/m/Y') }}

                                    </td>

                                    <td>
                                        @if($installment->is_paid)
                                            <span class="badge bg-primary">
                                                Pagado
                                            </span>
                                        @elseif($installment->paid_amount > 0)
                                            <span class="badge bg-warning text-dark">
                                                Parcial
                                            </span>
                                        @elseif($installment->due_date->isPast())
                                            <span class="badge bg-danger">
                                                Vencida
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                Pendiente
                                            </span>
                                        @endif

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                @endif

            </div>

        </div>

    </div>

</div>
