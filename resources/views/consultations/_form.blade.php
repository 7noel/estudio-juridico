@php
    $consultation = $consultation ?? null;
@endphp

<div class="row mb-3">
    <div class="col-md-3">
        <x-form.select
            name="service_type"
            label="Tipo de Servicio"
            :options="config('options.service_types')"
            :selected="$consultation->service_type ?? ''"
            required
        />
    </div>
    <div class="col-md-3">
        <x-form.select
            name="legal_specialty_id"
            label="Especialidad"
            :options="$specialties->pluck('name','id')->toArray()"
            :selected="$consultation->legal_specialty_id ?? ''"
            required
        />
    </div>

    <div class="col-md-3">
        <x-form.select
            name="legal_subject_id"
            label="Materia"
            :options="[]"
            :selected="$consultation->legal_subject_id ?? ''"
            required
        />
    </div>
</div>
{{-- CLIENTE + ABOGADO --}}
<div class="row mb-3">

    <div class="col-md-6">
        <x-form.autocomplete
            name="client_id"
            label="Cliente"
            :value="$consultation->client_id ?? ''"
            :text="$consultation->client->full_name ?? ''"
            required
        />
    </div>

    <div class="col-md-3">
        <x-form.select
            name="lawyer_id"
            label="Abogado"
            :options="$lawyers->pluck('name','id')->toArray()"
            :selected="$consultation->lawyer_id ?? ''"
            required
        />
    </div>

</div>

{{-- TITULO + MONTO --}}
<div class="row mb-3">

    <div class="col-md-6">
        <x-form.input
            name="title"
            label="Título"
            :value="$consultation->title ?? ''"
            required
        />
    </div>

    <div class="col-md-3">
        <x-form.input
            name="total_amount"
            label="Monto total"
            type="number"
            step="0.01"
            :value="$consultation->total_amount ?? ''"
            required
        />
    </div>

</div>

{{-- DESCRIPCIÓN --}}
<div class="row mb-3">

    <div class="col-md-9">
        <x-form.textarea
            name="description"
            label="Descripción"
        >{{ $consultation->description ?? '' }}</x-form.textarea>
    </div>

</div>

<hr>

{{-- CUOTAS --}}
@include('consultations.partials.installments')

@push('scripts')

<script>

function loadSubjects(specialtyId, selected = null){

    if(!specialtyId){
        $('select[name="legal_subject_id"]').html('<option value="">Seleccione</option>');
        return;
    }

    $.get("{{ route('legal-subjects.by-specialty') }}", {
        legal_specialty_id: specialtyId
    }, function(data){

        let options = '<option value="">Seleccione</option>';

        data.forEach(item => {
            let isSelected = selected == item.id ? 'selected' : '';
            options += `<option value="${item.id}" ${isSelected}>${item.name}</option>`;
        });

        $('select[name="legal_subject_id"]').html(options);
    });
}


/* ===========================
   CAMBIO DE ESPECIALIDAD
=========================== */
$(document).on('change', 'select[name="legal_specialty_id"]', function(){

    let specialtyId = $(this).val();

    loadSubjects(specialtyId);
});


/* ===========================
   MODO EDIT (PRECARGA)
=========================== */
$(function(){

    let specialtyId = $('select[name="legal_specialty_id"]').val();
    let selectedSubject = "{{ $consultation->legal_subject_id ?? '' }}";

    if(specialtyId){
        loadSubjects(specialtyId, selectedSubject);
    }

});


/* ===========================
   AUTOCOMPLETE CLIENTE
=========================== */
$(function(){
    $('#client_id_search').autocomplete({
        minLength: 2,
        delay: 200,
        source: function(request, response) {
            $.get(
                "{{ route('clients.search') }}",
                { q: request.term },
                function(data){
                    response(
                        data.map(item => ({
                            label: item.label,
                            value: item.label,
                            id: item.id
                        }))
                    );
                }
            );
        },
        select: function(event, ui){
            $('#client_id').val(ui.item.id);
        }
    });
});

/* ===========================
   CUOTAS DINÁMICAS
=========================== */

let i = {{ isset($consultation) ? $consultation->installments->count() : 0 }};

$('#add-installment').click(function(){

    let row = `
        <tr>
            <td>
                <input name="installments[${i}][amount]" class="form-control form-control-sm text-end" required>
            </td>
            <td>
                <input type="date" name="installments[${i}][due_date]" class="form-control form-control-sm">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger btn-remove">X</button>
            </td>
        </tr>
    `;

    $('#installments-table tbody').append(row);
    i++;
});

$(document).on('click', '.btn-remove', function(){
    $(this).closest('tr').remove();
});

</script>

@endpush