@php
    $legalSpecialty = $legalSpecialty ?? null;
@endphp

{{-- ESPECIALIDAD --}}
<div class="row mb-3">
    <div class="col-md-6">
        <x-form.input
            name="name"
            label="Especialidad"
            :value="$legalSpecialty->name ?? ''"
            required
            uppercase
        />
    </div>
</div>

<hr class="my-4">

<div class="col-md-6">
{{-- HEADER MATERIAS --}}
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h6 class="mb-0">Materias</h6>

        <x-form.button type="button" id="add-subject" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-plus"></i> Agregar
        </x-form.button>
    </div>

{{-- TABLA --}}

    <table class="table table-sm" id="subjects-table">
        <tbody>

            @if(isset($legalSpecialty) && $legalSpecialty->subjects->count())
                @foreach($legalSpecialty->subjects as $s)
                <tr>
                    <td>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">
                                <i class="bi bi-book"></i>
                            </span>

                            {{-- 🔥 SIN NAME --}}
                            <input
                                class="form-control subject-input text-uppercase"
                                value="{{ $s->name }}"
                                placeholder="Nombre de la materia"
                                required
                            >
                        </div>
                    </td>

                    <td style="width: 60px;" class="text-center">
                        <button type="button" class="btn btn-outline-danger btn-sm btn-remove">
                            <i class="bi bi-x"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            @else
                <tr class="no-data">
                    <td colspan="2" class="text-center text-muted">
                        No hay materias registradas
                    </td>
                </tr>
            @endif

        </tbody>
    </table>
</div>

@push('scripts')

<script>

/* ===========================
   AGREGAR MATERIA
=========================== */
$('#add-subject').click(function(){

    $('.no-data').remove();

    let row = `
        <tr>
            <td>
                <div class="input-group input-group-sm">
                    <span class="input-group-text">
                        <i class="bi bi-book"></i>
                    </span>

                    <input
                        class="form-control subject-input text-uppercase"
                        placeholder="Nombre de la materia"
                        required
                    >
                </div>
            </td>

            <td style="width: 60px;" class="text-center">
                <button type="button" class="btn btn-outline-danger btn-sm btn-remove">
                    <i class="bi bi-x"></i>
                </button>
            </td>
        </tr>
    `;

    $('#subjects-table tbody').append(row);
});


/* ===========================
   ELIMINAR MATERIA
=========================== */
$(document).on('click', '.btn-remove', function(){

    $(this).closest('tr').remove();

    if($('#subjects-table tbody tr').length === 0){
        $('#subjects-table tbody').html(`
            <tr class="no-data">
                <td colspan="2" class="text-center text-muted">
                    No hay materias registradas
                </td>
            </tr>
        `);
    }

});


/* ===========================
   REINDEX ANTES DE ENVIAR
=========================== */
$('form').on('submit', function(){

    let index = 0;

    $('#subjects-table tbody tr').each(function(){

        let input = $(this).find('.subject-input');

        let value = input.val().trim();

        if(value !== ''){

            // 🔥 eliminar cualquier name previo
            input.removeAttr('name');

            // 🔥 asignar nuevo name limpio
            input.attr('name', `subjects[${index}][name]`);

            index++;
        }

    });

});
</script>

@endpush