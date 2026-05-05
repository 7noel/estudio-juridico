@extends('layouts.app')

@section('content')

<div class="card shadow-sm">

	<div class="card-header d-flex justify-content-between align-items-center">

	    {{-- IZQUIERDA --}}
	    <div class="d-flex align-items-center gap-2">
	        <h6 class="mb-0">
	            Consulta #{{ $consultation->id }}
	        </h6>

	        @php
	            $status = $consultation->status;
	            $label = config('options.consultation_statuses')[$status] ?? $status;
	            $color = config('options.consultation_status_colors')[$status] ?? 'secondary';
	        @endphp

	        <span class="badge bg-{{ $color }}">
	            {{ $label }}
	        </span>
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
		                <small class="text-muted">Tipo de servicio</small><br>
		                <strong>{{ config('options.service_types')[$consultation->service_type] ?? '-' }}</strong>
		            </div>

		            <div class="col-md-3">
		                <small class="text-muted">Especialidad</small><br>
		                <strong>{{ $consultation->specialty->name ?? '-' }}</strong>
		            </div>

		            <div class="col-md-3">
		                <small class="text-muted">Materia</small><br>
		                <strong>{{ $consultation->subject->name ?? '-' }}</strong>
		            </div>

		            <div class="col-md-3">
		                <small class="text-muted">Abogado</small><br>
		                <strong>{{ $consultation->lawyer->name ?? '-' }}</strong>
		            </div>
		        </div>

		        <div class="row mb-2">
		            <div class="col-md-6">
		                <small class="text-muted">Cliente</small><br>
		                <strong>{{ $consultation->client->full_name }}</strong>
		            </div>

		            <div class="col-md-6">
		                <small class="text-muted">Título</small><br>
		                <strong>{{ $consultation->title }}</strong>
		            </div>
		        </div>

		        <div class="row">
		            <div class="col-md-12">
		                <small class="text-muted">Descripción</small><br>
		                <div class="border rounded p-2 bg-light">
		                    {{ $consultation->description ?: 'Sin descripción' }}
		                </div>
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

		                @if($consultation->is_paid)
		                    <span class="badge bg-success">Pagado</span>
		                @else
		                    <span class="badge bg-danger">Pendiente</span>
		                @endif

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
                            <span class="badge bg-success">Pagado</span>
                        @elseif($inst->paid_amount > 0)
                            <span class="badge bg-warning text-dark">Parcial</span>
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