@extends('layouts.app')

@section('content')

<div class="card shadow-sm">

	<div class="card-header d-flex justify-content-between align-items-center">

	    {{-- IZQUIERDA --}}
	    <div class="d-flex align-items-center gap-2">
	        <h6 class="mb-0">
	            Consulta #{{ $consultation->id }}
	        </h6>

	    </div>

	    {{-- DERECHA --}}
	    <div class="d-flex gap-2">

	        @if(!$consultation->case && $consultation->status != 'rejected')

	            {{-- Generar caso --}}
	            @if(in_array($consultation->status, ['quoted', 'evaluated']))
	                <button
	                    type="button"
	                    class="btn btn-sm btn-outline-success btn-generate-case"
	                    data-id="{{ $consultation->id }}"
	                >
	                    <i class="bi bi-briefcase"></i> Generar caso
	                </button>
	            @endif

	            {{-- Evaluar (futuro) --}}
	            @if($consultation->status == 'assigned' && false)
	                <button
	                    type="button"
	                    class="btn btn-sm btn-outline-warning btn-mark-evaluated"
	                >
	                    <i class="bi bi-search"></i> Evaluar
	                </button>
	            @endif

	            {{-- Rechazar --}}
	            @if(in_array($consultation->status, ['quoted', 'evaluated']))
	                <button
	                    type="button"
	                    class="btn btn-outline-danger btn-sm btn-reject"
	                    data-id="{{ $consultation->id }}"
	                >
	                    <i class="bi bi-x-circle"></i> Rechazar
	                </button>
	            @endif

	        @endif

	    </div>

	</div>

    <div class="card-body">

        {{-- DATOS --}}
		<div class="card shadow-sm mb-3">

		    <div class="card-header bg-white">
		        <strong>Información de la consulta</strong>
		    </div>

		    <div class="card-body">

		        <div class="row mb-2">
		            <div class="col-md-3">
		                <strong>Tipo de servicio:</strong><br>
		                <span>{{ config('options.service_types')[$consultation->service_type] ?? '-' }}</span>
		            </div>

		            <div class="col-md-3">
		                <strong>Especialidad:</strong><br>
		                <span>{{ $consultation->specialty->name ?? '-' }}</span>
		            </div>

		            <div class="col-md-3">
		                <strong>Materia:</strong><br>
		                <span>{{ $consultation->subject->name ?? '-' }}</span>
		            </div>

		            <div class="col-md-3">
		                <strong>Abogado:</strong><br>
		                <span>{{ $consultation->lawyer->name ?? '-' }}</span>
		            </div>
		        </div>

		        <div class="row mb-2">
		            <div class="col-md-6">
		                <strong>Cliente:</strong><br>
		                <span>{{ $consultation->client->full_name }}</span>
		            </div>
		            <div class="col-md-3">
		                <strong>Estado:</strong><br>
		                <span class="badge bg-{{ config('options.consultation_status_colors')[$consultation->status] }}">
		                	{{ config('options.consultation_statuses')[$consultation->status] }}
		                </span>
		            </div>

		        </div>

		        <div class="row">
		            <div class="col-md-12">
		                <strong>Título:</strong><br>
		                <span>{{ $consultation->title }}</span>
		            </div>
		        </div>
		        <div class="row">
		            <div class="col-md-12">
		                <strong>Descripción:</strong><br>
		                <span>
		                    {{ $consultation->description ?: 'Sin descripción' }}
		                </span>
		            </div>
		        </div>

		    </div>
		</div>

		{{-- RESUMEN FINANCIERO --}}
		<div class="card shadow-sm mb-3">

		    <div class="card-header bg-white">
		        <strong>Resumen financiero</strong>
		    </div>

		    <div class="card-body">

		        <div class="row text-center">

		            <div class="col-md-3">
		                <small>Total</small>
		                <h5 class="mb-0">S/ {{ number_format($consultation->total_amount,2) }}</h5>
		            </div>

		            <div class="col-md-3">
		                <small>Pagado</small>
		                <h5 class="mb-0 text-success">
		                    S/ {{ number_format($consultation->paid_amount,2) }}
		                </h5>
		            </div>

		            <div class="col-md-3">
		                <small>Pendiente</small>
		                <h5 class="mb-0 text-danger">
		                    S/ {{ number_format($consultation->pending_amount,2) }}
		                </h5>
		            </div>

		            <div class="col-md-3">
		                <small>Estado</small><br>

		                <span class="badge bg-{{ $consultation->financial_status_color }}">
                            {{ $consultation->financial_status_label }}
                        </span>

		            </div>

		        </div>

		    </div>
		</div>

        {{-- CUOTAS --}}

        <h6 class="mb-2">Cuotas</h6>

        <table class="table table-sm table-bordered">
            <thead>
                <tr>
                    <th class="text-center">#</th>
                    <th class="text-center">Monto</th>
                    <th class="text-center">Pagado</th>
                    <th class="text-center">Saldo</th>
                    <th class="text-center">Vencimiento</th>
                    <th class="text-center">Estado</th>
                    <th class="text-center"></th>
                </tr>
            </thead>
            <tbody>

                @foreach($consultation->installments as $inst)
                <tr>
                    <td class="text-center">{{ $inst->installment_number }}</td>
                    <td class="text-center">{{ number_format($inst->amount,2) }}</td>
                    <td class="text-center">{{ number_format($inst->paid_amount,2) }}</td>
                    <td class="text-center">{{ number_format($inst->pending_amount,2) }}</td>
                    <td class="text-center">{{ $inst->due_date->format('d/m/Y') }}</td>

                    <td class="text-center">
                        @if($inst->is_paid)
                            <span class="badge bg-primary">Pagado</span>
                        @elseif($inst->paid_amount > 0)
                            <span class="badge bg-warning text-dark">Parcial</span>
                        @elseif($inst->due_date->isPast())
                            <span class="badge bg-danger">Vencida</span>
                        @else
                            <span class="badge bg-secondary">Pendiente</span>
                        @endif
                    </td>

                    <td class="text-center">
                    	@if($consultation->status == 'rejected')
                            <button class="btn btn-outline-success btn-sm btn-pay" disabled>
                                <i class="bi bi-cash"></i> Pagar
                            </button>
                    	@elseif(!$inst->is_paid)
                            <button
                                class="btn btn-outline-success btn-sm btn-pay"
                                data-id="{{ $inst->id }}"
                                data-consultation="{{ $consultation->id }}"
                                data-pending="{{ $inst->pending_amount }}"
                            >
                                <i class="bi bi-cash"></i> Pagar
                            </button>
						@else
                            <button
                                class="btn btn-outline-secondary btn-sm btn-pay"
                                data-id="{{ $inst->id }}"
                                data-consultation="{{ $consultation->id }}"
                                data-pending="{{ $inst->pending_amount }}"
                            >
                                <i class="bi bi-eye"></i> Ver
                            </button>
						@endif
                        @if(!$inst->is_paid)
                        @endif
                    </td>
                </tr>
                @endforeach

            </tbody>
        </table>

    </div>

</div>

@include('consultations.partials.payments')

<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">

            <div class="modal-header">
                <h6 class="modal-title">Confirmar acción</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center">
                <p id="confirmMessage"></p>
            </div>

            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-primary btn-sm" id="confirmActionBtn">
                    Confirmar
                </button>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                    Cancelar
                </button>
            </div>

        </div>
    </div>
</div>

@endsection