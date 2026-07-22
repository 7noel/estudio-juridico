<div class="card shadow-sm mt-3">

    <div class="card-header d-flex justify-content-between align-items-center">

        <strong>Seguimientos</strong>

        @if(!in_array($consultation->status, ['accepted', 'rejected']))
            <button
                type="button"
                class="btn btn-sm btn-outline-primary"
                data-bs-toggle="modal"
                data-bs-target="#followUpModal">

                <i class="bi bi-plus-circle"></i>

                Nuevo seguimiento

            </button>
        @endif

    </div>

    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-sm table-hover align-middle mb-0">

                <thead class="table-light">

                    <tr>

                        <th width="90">Fecha</th>

                        <th width="150">Tipo</th>

                        <th width="170">Resultado</th>

                        <th width="120">Próximo</th>

                        <th>Observaciones</th>

                        <th width="90" class="text-center">Acciones</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($consultation->followUps as $followUp)

                        <tr>

                            <td>

                                {{ $followUp->contact_date->format('d/m/Y') }}

                            </td>

                            <td>

                                {{ config('options.communication_types')[$followUp->communication_type] ?? '-' }}

                            </td>

                            <td>

                                <span class="badge bg-{{ config('options.follow_up_result_colors')[$followUp->result] }}">

                                    {{ config('options.follow_up_results')[$followUp->result] }}

                                </span>

                            </td>

                            <td>

                                @if($followUp->next_contact_date)

                                    {{ $followUp->next_contact_date->format('d/m/Y') }}

                                @else

                                    —

                                @endif

                            </td>

                            <td class="text-wrap">

                                {!! nl2br(e($followUp->notes)) !!}

                            </td>

                            <td class="text-center">
                                @if(!in_array($consultation->status, ['accepted', 'rejected']))

                                <form
                                    action="{{ route('follow-ups.destroy', $followUp) }}"
                                    method="POST"
                                    onsubmit="return confirm('¿Eliminar este seguimiento?')"
                                    class="d-inline"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="btn btn-outline-danger btn-sm"
                                        title="Eliminar"
                                    >

                                        <i class="bi bi-trash"></i>

                                    </button>

                                </form>
                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="text-center text-muted py-4"
                            >

                                No existen seguimientos registrados.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@include('consultations.partials.follow-up-modal')

@if ($errors->any())
<script>
document.addEventListener('DOMContentLoaded', function () {
    new bootstrap.Modal(document.getElementById('followUpModal')).show();
});
</script>
@endif