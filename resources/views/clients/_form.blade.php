{{-- Documento --}}

<div class="row mb-3">
    <div class="col-md-2">
        <x-form.select
        name="document_type"
        label="Tipo documento"
        :options="config('options.client_document_types')"
        :selected="$client->document_type ?? ''"
        required
        />
    </div>
    <div class="col-md-2">
        <x-form.input
        name="document_number"
        label="Número documento"
        :value="$client->document_number ?? ''"
        required
        />
    </div>

{{-- Nombre completo --}}

    <div class="col-md-4">
        <x-form.input
        name="full_name"
        label="Nombre completo"
        :value="$client->full_name ?? ''"
        required
        />
    </div>
</div>

{{-- Contacto --}}

<div class="row mb-3">
    <div class="col-md-2">
        <x-form.input
        name="mobile"
        label="Celular"
        :value="$client->mobile ?? ''"
        />
    </div>
    <div class="col-md-2">
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
    <div class="col-md-4">
        <x-form.input
        name="address"
        label="Dirección"
        :value="$client->address ?? ''"
        />
    </div>

    <div class="col-md-3">
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

$(function() {

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

</script>

@endpush