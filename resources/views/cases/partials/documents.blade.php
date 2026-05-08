<div class="card mt-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <strong>Documentos</strong>

        <button class="btn btn-sm btn-outline-primary" id="btnAddDocument"
                data-bs-toggle="modal"
                data-bs-target="#modalDocument">
            <i class="bi bi-cloud-arrow-up"></i> Subir
        </button>
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
                        <select name="document_type" class="form-control mb-2">
                            @foreach(config('options.document_types') as $k => $v)
                                <option value="{{ $k }}">{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-2">
                        <label>Título</label>
                        <input type="text" name="title" class="form-control mb-2" placeholder="">
                    </div>

                    <div class="mb-2" id="fileInputWrapper">
                        <label>Documento</label>
                        <input type="file" name="file" class="form-control">
                    </div>
                    <div class="mb-3 d-none" id="currentFileWrapper">
                        <label>Documento actual</label><br>
                        <a href="#" target="_blank" id="currentFileLink" class="btn btn-sm btn-outline-secondary">
                            Ver archivo actual
                        </a>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">Guardar</button>
                </div>

            </form>

        </div>
    </div>
</div>