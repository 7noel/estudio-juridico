<div class="card mt-3 shadow-sm border-1">

    <div class="card-body border-bottom">
        @php

            $documents = $case->documents;

            $counts = [];

            foreach(config('options.document_types') as $key => $label){

                $counts[$key] = $documents
                    ->where('document_type',$key)
                    ->count();

            }

        @endphp


        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3">

            <div class="flex-grow-1">

                <label class="form-label fw-semibold mb-2">

                    Filtrar documentos

                </label>

                <div
                    class="d-flex flex-wrap gap-2"
                    id="documentFilters">

                    <button
                        class="btn btn-sm btn-primary active"
                        data-filter="all">

                        Todos

                        <span class="badge bg-light text-dark ms-1">

                            {{ $documents->count() }}

                        </span>

                    </button>

                    @foreach(config('options.document_types') as $key=>$label)

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
                        id="btnAddDocument"
                        data-bs-toggle="modal"
                        data-bs-target="#modalDocument">

                        <i class="bi bi-cloud-arrow-up"></i>

                        Subir documento

                    </button>

                </div>

            @endif

        </div>


        <div class="table-responsive mt-3">

            <table
                class="table table-sm table-hover align-middle mb-0"
                id="documentsTable">

                <thead class="table-light">

                    <tr>

                        <th style="width:15%" class="text-center">
                            Fecha
                        </th>
                        <th style="width:15%" class="text-center">
                            Tipo
                        </th>
                        <th style="width:20%" class="text-center">
                            Título
                        </th>


                        <th class="text-center">
                            Archivo
                        </th>

                        @if($canManageCaseContent)

                            <th
                                class="text-end text-center"
                                style="width:200px">

                                Acciones

                            </th>

                        @endif

                    </tr>

                </thead>

                <tbody>
                    @forelse($documents as $doc)

                    <tr
                        class="document-item text-center"
                        data-type="{{ $doc->document_type }}">


                        <td data-label="Fecha">

                                {{ $doc->created_at?->translatedFormat('d M Y') }}
                                <small class="text-muted fw-normal">
                                    · {{ $doc->created_at?->format('g:i A') }}
                                </small>

                        </td>

                        {{-- TIPO --}}
                        <td data-label="Tipo" class="text-center">

                            {{ config('options.document_types')[$doc->document_type] ?? '-' }}

                        </td>

                        {{-- TÍTULO --}}
                        <td data-label="Título" class="text-center">

                            <strong>

                                {{ $doc->title }}

                            </strong>

                        </td>

                        {{-- ARCHIVO --}}
                        <td data-label="Archivo" class="text-center">

                            <a
                                href="{{ $doc->url }}"
                                target="_blank">

                                {{ $doc->file_name }}

                            </a>

                        </td>

                        {{-- ACCIONES --}}
                        @if($canManageCaseContent)

                            <td
                                data-label="Acciones"
                                class="text-center">

                                <div class="d-flex justify-content-end gap-2">

                                    <button
                                        class="btn btn-sm btn-outline-primary btn-edit-doc"

                                        data-id="{{ $doc->id }}"
                                        data-title="{{ $doc->title }}"
                                        data-type="{{ $doc->document_type }}"
                                        data-file="{{ $doc->file_path }}"
                                        data-file_name="{{ $doc->file_name }}">

                                        <i class="bi bi-pencil"></i>

                                        Editar

                                    </button>

                                    <button
                                        class="btn btn-sm btn-outline-danger btn-delete-doc"

                                        data-id="{{ $doc->id }}">

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
                            colspan="{{ $canManageCaseContent ? 4 : 3 }}"
                            class="text-center py-5">

                            <i class="bi bi-folder2-open fs-1 text-muted d-block mb-3"></i>

                            <h6 class="text-muted mb-1">

                                No hay documentos registrados

                            </h6>

                            <small class="text-muted">

                                Aún no se ha subido ningún documento para este expediente.

                            </small>

                        </td>

                    </tr>

                    @endforelse
                </tbody>

            </table>

        </div>

    </div>
</div>

<div class="modal fade" id="modalDocument">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="form-document">
                @csrf
                <input type="hidden" id="doc_id">
                <div class="modal-header">
                    <h6 id="docModalTitle">Subir documento</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label>Tipo</label>
                        <select name="document_type" class="form-select form-select-sm mb-2">
                            <option value=""> Seleccionar </option>
                            @foreach(config('options.document_types') as $k => $v)
                                <option value="{{ $k }}">{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <label>Título</label>
                        <input type="text" name="title" class="form-control form-control-sm mb-2 text-uppercase" placeholder="">
                    </div>
                    <div class="mb-2" id="fileInputWrapper">
                        <label>Documento</label>
                        <input type="file" name="file" class="form-control form-control-sm">
                    </div>
                    <div class="mb-3 d-none" id="currentFileWrapper">
                        <label>Documento actual</label><br>
                        <a href="#" target="_blank" id="currentFileLink" class="btn btn-sm btn-outline-secondary">
                            Ver archivo actual
                        </a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-sm btn-outline-primary"> <i class="bi bi-save"></i> Guardar</button>
                </div>

            </form>

        </div>
    </div>
</div>

@push('scripts')
<script>
// let caseId = {{ $case->id }};
let mode = 'create'; // create | edit
let currentDocumentId = null;

// =================
// CREAR / EDITAR
// =================
$('#form-document').submit(function(e){
    e.preventDefault();

    let id = $('#doc_id').val();

    let formData = new FormData(this);

    let url = id
        ? `/documents/${id}`
        : `/cases/${caseId}/documents`;

    let method = id ? 'POST' : 'POST';

    if(id){
        formData.append('_method', 'PUT');
    }

    $.ajax({
        url: url,
        method: method,
        data: formData,
        processData: false,
        contentType: false,
        success: function(){
            location.reload();
        }
    });
});

// =================
// EDITAR
// =================
$(document).on('click', '.btn-edit-doc', function(){
    mode = 'edit';

    const id = $(this).data('id');
    currentDocumentId = id;

    const title = $(this).data('title');
    const type = $(this).data('type');
    const file = $(this).data('file'); // IMPORTANTE
    const file_name = $(this).data('file_name'); // IMPORTANTE

    $('#doc_id').val(currentDocumentId);
    $('[name="title"]').val(title);
    $('[name="document_type"]').val(type);

    // 🔥 OCULTAR INPUT FILE
    $('#fileInputWrapper').hide();

    // 🔥 MOSTRAR LINK ACTUAL
    $('#currentFileWrapper').removeClass('d-none');
    $('#currentFileLink').attr('href', '/storage/' + file);
    $('#currentFileLink').text('Ver: ' + file_name);


    $('#docModalTitle').text('Editar documento');

    $('#modalDocument').modal('show');
});

// =================
// NUEVO
// =================
$('#btnAddDocument').click(function(){
    mode = 'create';
    currentDocumentId = null;

    $('#doc_id').val('');
    $('#form-document')[0].reset();
    $('#docModalTitle').text('Subir documento');

    // 🔥 MOSTRAR INPUT FILE
    $('#fileInputWrapper').show();

    // 🔥 OCULTAR LINK
    $('#currentFileWrapper').addClass('d-none');
});

// =================
// ELIMINAR
// =================
$(document).on('click', '.btn-delete-doc', function(){

    if(!confirm('¿Eliminar documento?')) return;

    let id = $(this).data('id');

    $.ajax({
        url: `/documents/${id}`,
        type: 'DELETE',
        data: {_token: '{{ csrf_token() }}'},
        success: function(){
            location.reload();
        }
    });
});

// ===================================
// FILTRAR DOCUMENTOS
// ===================================

$(document).on(
    'click',
    '#documentFilters .btn',
    function(){

        $('#documentFilters .btn')
            .removeClass('btn-primary active')
            .addClass('btn-outline-secondary');

        $(this)
            .removeClass('btn-outline-secondary')
            .addClass('btn-primary active');

        const filter = $(this).data('filter');

        if(filter === 'all'){

            $('.document-item').show();

            return;

        }

        $('.document-item').hide();

        $('.document-item[data-type="'+filter+'"]').show();

    }
);

</script>
@endpush


@push('styles')
<style>

#documentsTable td,
#documentsTable th{
    vertical-align: middle;
}

#documentsTable tbody tr{
    transition: background .15s;
}

#documentsTable tbody tr:hover{
    background: #f8f9fa;
}

/* ==========================
   CELULAR
========================== */

@media (max-width:768px){

    #documentsTable thead{
        display:none;
    }

    #documentsTable,
    #documentsTable tbody,
    #documentsTable tr,
    #documentsTable td{

        display:block;
        width:100%;

    }

    #documentsTable tr{

        margin-bottom:1rem;
        border:1px solid #dee2e6;
        border-radius:.6rem;
        overflow:hidden;
        background:#fff;
        box-shadow:0 .125rem .25rem rgba(0,0,0,.05);

    }

    #documentsTable td{

        border:none;
        border-bottom:1px solid #f1f1f1;
        padding:.75rem 1rem;
        text-align:left !important;

    }

    #documentsTable td:last-child{

        border-bottom:none;

    }

    #documentsTable td::before{

        content:attr(data-label);

        display:block;

        font-size:.72rem;
        font-weight:600;

        color:#6c757d;

        text-transform:uppercase;

        margin-bottom:.25rem;

    }

    #documentsTable .btn{

        flex:1;

    }

}

</style>
@endpush