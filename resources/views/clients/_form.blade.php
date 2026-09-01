{{-- Documento --}}

@if(($client->is_temporary_document ?? false))
<div class="alert alert-warning py-2 small mb-3">
    <i class="bi bi-exclamation-triangle-fill me-1"></i>
    Este cliente tiene un <strong>documento temporal</strong> (<code>{{ $client->document_number }}</code>).
    Cuando proporcione su documento, cambie el <strong>Tipo de documento</strong> e ingrese el número real.
</div>
@endif

<div class="row mb-3">
    <div class="col-md-3">
        <x-form.select
        name="document_type"
        label="Tipo documento"
        :options="config('options.client_document_types')"
        :selected="$client->document_type ?? '1'"
        required
        />
    </div>
    <div class="col-md-3">
        <x-form.input
        name="document_number"
        label="Número documento"
        :value="$client->document_number ?? ''"
        />
        <div class="form-text text-muted small d-none" id="document_number_hint">
            Se generará un documento temporal (TMP + fecha y hora).
        </div>
    </div>

{{-- Nombre completo --}}

    <div class="col-md-6">
        <x-form.input
        name="full_name"
        label="Nombre completo"
        :value="$client->full_name ?? ''"
        required
        uppercase
        />
    </div>
</div>

{{-- Contacto --}}

<div class="row mb-3">
    <div class="col-md-3">
        <x-form.input
        name="mobile"
        label="Celular"
        type="tel"
        inputmode="tel"
        maxlength="16"
        placeholder="999 123 456 · +34 612 345 678"
        :value="$client->mobile ?? ''"
        />
        <div class="form-text text-muted small">
            Perú: 9 dígitos (ej. 999 123 456). Extranjero: +código de país + número (ej. +34 612 345 678).
        </div>
    </div>
    <div class="col-md-3">
        <x-form.input
        name="phone"
        label="Teléfono fijo"
        :value="$client->phone ?? ''"
        />
    </div>
    <div class="col-md-3">
        <x-form.input
        name="email"
        type="email"
        label="Correo"
        :value="$client->email ?? ''"
        />
    </div>
</div>


{{-- Dirección --}}

<div class="row mb-3">
    <div class="col-md-6">
        <x-form.input
        name="address"
        label="Dirección"
        :value="$client->address ?? ''"
        />
    </div>

    <div class="col-md-6">
        <x-form.autocomplete
        name="ubigeo_code"
        label="Distrito"
        :value="$client->ubigeo_code ?? ''"
        :text="$client->ubigeo_text ?? ''"
        />
    </div>
</div>

@push('scripts')

<script>
$(document).on('input', '[name="phone"]', function(){

    this.value = this.value
        .replace(/\D/g, '')
        .slice(0, 9);

});

$(document).on('input', '[name="mobile"]', function(){

    // Solo dígitos y el signo + (espacios, guiones y paréntesis se eliminan solos)
    this.value = this.value.replace(/[^\d+]/g, '');

});

// =====================================
// Documento temporal (cliente sin documento)
// =====================================

function toggleTemporaryDocument(){
    let isTemp = $('#document_type').val() === 'T';

    $('#document_number')
        .prop('disabled', isTemp)
        .prop('required', !isTemp);

    $('#document_number_hint').toggleClass('d-none', !isTemp);
}

$('#document_type').on('change', toggleTemporaryDocument);

$(function(){
    toggleTemporaryDocument();
});

$(function(){
    $('#ubigeo_code_search').autocomplete({
        minLength: 2,
        delay: 200,
        source: function(request, response) {
            $.get(
                "{{ route('ubigeos.search') }}",
                { term: request.term },
                function(data){
                response(
                    data.map(item => ({
                        label: item.text,
                        value: item.text,
                        id: item.id
                    }))
                );
                }
            );
        },
        select: function(event, ui){
            $('#ubigeo_code').val(ui.item.id);
        }
    });
});

$(function(){
    $('#document_number').on('change', function(){
        let doc = $(this).val();
        let type = $('#document_type').val();
        if (type == '1' && doc.length == 8){
            getDataPadron(doc, type);
        }
        if (type == '6' && doc.length == 11){
            getDataPadron(doc, type);
        }
    });
});

function getDataPadron(doc, type){
    let urls = {
        "1": `https://dniruc.apisperu.com/api/v1/dni/${doc}?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6Im5vZWwubG9nYW5AZ21haWwuY29tIn0.pSSHu1Rh3RUgPubnjemiDNyMAN0ZjgTCXaupa8VsEYY`,
        "6": `https://dniruc.apisperu.com/api/v1/ruc/${doc}?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6Im5vZWwubG9nYW5AZ21haWwuY29tIn0.pSSHu1Rh3RUgPubnjemiDNyMAN0ZjgTCXaupa8VsEYY`
    };
    $.get(urls[type], function(data){
        if (!data) return;
        if (!data.success) return;
        if (type == '6'){
            $('#full_name').val(data.razonSocial);
            if (data.ubigeo){
                $('#address').val(data.direccion);
                $('#ubigeo_code').val(data.ubigeo);
            }
        }else{
            let fullname =
            data.apellidoPaterno + ' ' +
            data.apellidoMaterno + ' ' +
            data.nombres;
            $('#full_name').val(fullname);
        }
    });
}

</script>

@endpush