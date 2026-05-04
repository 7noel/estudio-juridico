<a href="{{ route('consultations.edit', $r->id) }}" class="btn btn-sm btn-outline-primary">Editar</a>

<button class="btn btn-sm btn-outline-danger btn-delete" data-id="{{ $r->id }}">Eliminar</button>

<script>
$(document).on('click', '.btn-delete', function(){
    let id = $(this).data('id');

    if(confirm('Eliminar?')){
        $.ajax({
            url: '/consultations/' + id,
            type: 'DELETE',
            data: {_token: '{{ csrf_token() }}'},
            success: () => location.reload()
        });
    }
});
</script>