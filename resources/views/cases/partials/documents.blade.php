<div class="card mt-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <strong>Documentos</strong>
        @if($canManageCaseContent)
        <button class="btn btn-sm btn-outline-primary" id="btnAddDocument"
                data-bs-toggle="modal"
                data-bs-target="#modalDocument">
            <i class="bi bi-cloud-arrow-up"></i> Subir
        </button>
        @endif
    </div>

    <div class="card-body">

        @forelse($case->documents as $doc)
            <div class="border rounded p-2 mb-2">

                <div class="d-flex justify-content-between">

                    <div>
                        <strong>{{ $doc->title }}</strong><br>
                        <small>{{ config('options.document_types')[$doc->document_type] }}</small><br>

                        <a href="{{ $doc->url }}" target="_blank">
                            {{ $doc->file_name }}
                        </a>
                    </div>

                    @if($canManageCaseContent)
                    <div class="text-end">

                        <button class="btn btn-sm btn-outline-success btn-edit-doc"
                                data-id="{{ $doc->id }}"
                                data-title="{{ $doc->title }}"
                                data-type="{{ $doc->document_type }}"
                                data-file="{{ $doc->file_path }}"
                                data-file_name="{{ $doc->file_name }}">
                            <i class="bi bi-pencil"></i> Editar
                        </button>

                        <button class="btn btn-sm btn-outline-danger btn-delete-doc"
                                data-id="{{ $doc->id }}">
                            <i class="bi bi-trash"></i> Eliminar
                        </button>

                    </div>
                    @endif

                </div>

            </div>
        @empty
            <div class="text-muted">No hay documentos</div>
        @endforelse

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
                        <select name="document_type" class="form-control form-control-sm mb-2">
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

</script>
@endpush