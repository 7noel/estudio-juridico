@extends('layouts.app')

@section('content')

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between">
        <h6 class="mb-0" id="pageTitle">
            <i class="bi bi-chat-left-text"></i> Consultas
        </h6>

        @can('create', App\Models\Consultation::class)
        <a href="{{ route('consultations.create') }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-plus"></i> Nuevo
        </a>
        @endcan
    </div>

    <div class="card-body pt-3 pb-0">
        <ul class="nav nav-pills mb-2" id="workMode">

            <li class="nav-item">
                <a href="#"
                   class="nav-link active"
                   data-mode="consultations">

                    <i class="bi bi-search"></i>

                    Buscar consultas

                </a>
            </li>

            <li class="nav-item">

                <a href="#"
                   class="nav-link"
                   data-mode="followups">

                    <i class="bi bi-telephone"></i>

                    Gestionar seguimientos

                </a>

            </li>

        </ul>
        <div id="consultationStatusBadges">
            <div class="mt-3 d-flex flex-wrap gap-2">

                <span class="badge bg-secondary filter-quick p-2" data-status="">
                    Todas: <span id="stat_all">0</span>
                </span>
                <span class="badge bg-primary filter-quick p-2" data-status="new">
                    Nuevos: <span id="stat_new">0</span>
                </span>

                <span class="badge bg-warning text-dark filter-quick p-2" data-status="prospect">
                    Prospectos: <span id="stat_prospect">0</span>
                </span>

                <span class="badge bg-success filter-quick p-2" data-status="accepted">
                    Aceptados: <span id="stat_accepted">0</span>
                </span>

                <span class="badge bg-danger filter-quick p-2" data-status="rejected">
                    Rechazados: <span id="stat_rejected">0</span>
                </span>

            </div>
        </div>

        <div id="consultationFilters">
            
            <div class="row mb-1 mt-3">

                {{-- Abogado --}}
                <div class="col-md">
                    <label>Abogado</label>
                    <select id="filter_lawyer" class="form-control form-control-sm">
                        <option value="">Todos</option>
                        @foreach($lawyers as $lawyer)
                            <option value="{{ $lawyer->id }}">{{ $lawyer->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Tipo de servicio --}}
                <div class="col-md">
                    <label>Tipo de servicio</label>
                    <select id="filter_service_type" class="form-control form-control-sm">
                        <option value="">Todos</option>
                        @foreach(config('options.service_types') as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Especialidad --}}
                <div class="col-md">
                    <label>Especialidad</label>
                    <select id="filter_specialty" class="form-control form-control-sm">
                        <option value="">Todas</option>
                        @foreach($specialties as $sp)
                            <option value="{{ $sp->id }}">{{ $sp->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Fecha desde --}}
                <div class="col-md">
                    <label>Desde</label>
                    <input type="date" id="filter_from" class="form-control form-control-sm">
                </div>

                {{-- Fecha hasta --}}
                <div class="col-md">
                    <label>Hasta</label>
                    <input type="date" id="filter_to" class="form-control form-control-sm">
                </div>

                {{-- Botón limpiar --}}
                <div class="col-md d-flex align-items-end">
                    <button id="btn-clear-filters" class="btn btn-outline-secondary btn-sm w-100">
                        Limpiar
                    </button>
                </div>

            </div>

        </div>


        <div id="followUpFilters" class="mb-1 mt-3 d-none">

            <div class="flex flex-wrap gap-2" id="followUpStats">

                <span class="badge bg-secondary follow-filter p-2" data-follow="">
                    Todos: <span id="stat_follow_all">0</span>
                </span>

                <span class="badge bg-primary follow-filter p-2" data-follow="today">
                    Hoy: <span id="stat_follow_today">0</span>
                </span>

                <span class="badge bg-warning text-dark follow-filter p-2" data-follow="overdue">
                    Vencidos: <span id="stat_follow_overdue">0</span>
                </span>

                <span class="badge bg-info text-dark follow-filter p-2" data-follow="week">
                    Próx. 7 días: <span id="stat_follow_week">0</span>
                </span>

                <span class="badge bg-dark follow-filter p-2" data-follow="none">
                    Sin seguimiento: <span id="stat_follow_none">0</span>
                </span>

                <span class="badge bg-success follow-filter p-2" data-follow="accepted">
                    Aceptados: <span id="stat_follow_accepted">0</span>
                </span>

                <span class="badge bg-danger follow-filter p-2" data-follow="rejected">
                    Rechazados: <span id="stat_follow_rejected">0</span>
                </span>

            </div>

        </div>
    </div>


    <div class="card-body">
        <div class="table-responsive">
            <table id="consultationsTable" class="table table-sm table-bordered table-striped"></table>
        </div>
    </div>
</div>

@include('consultations.partials.follow-up-modal')

@endsection


@push('scripts')

<script>
let table; // 🔥 GLOBAL

$(function(){


    table = $('#consultationsTable').DataTable({
        processing: true,
        serverSide: true,

        ajax: {
            url: "{{ route('consultations.data') }}", // 👈 mantenemos tu ruta
            data: function(d){
                d.status = consultationStatus;
                d.lawyer_id = $('#filter_lawyer').val();
                d.date_from = $('#filter_from').val();
                d.date_to = $('#filter_to').val();
                d.service_type = $('#filter_service_type').val();
                d.legal_specialty_id = $('#filter_specialty').val();
                d.work_mode = workMode;
                d.follow_up = followUpFilter;
            }
        },

        columns: [
            { data: 'id', title: 'ID' },
            { data: 'client', name: 'client.full_name', title: 'Cliente' },
            { data: 'lawyer', name: 'lawyer.name', title: 'Abogado' },
            { data: 'last_follow_up_at', title: 'Últ. contacto' },
            { data: 'last_follow_up_result', name:'last_follow_up_result', title: 'Resultado' },
            { data: 'next_follow_up_at', title: 'Próx. contacto' },
            { data: 'service_type', title: 'Tipo Servicio' },
            { data: 'specialty', name: 'specialty.name', title: 'Especialidad' },
            { data: 'subject', name: 'subject.name', title: 'Materia' },
            { data: 'status', title: 'Estado' },
            { data: 'case_link', name: 'case.id', title: 'Caso' },
            { data: 'total_amount', title: 'Monto' },
            { data: 'created_at', title: 'Fecha' },
            { data: 'actions', title: 'Acciones', orderable: false, searchable: false }
        ],

        columnDefs: [
            { className: "text-center", targets: [0,3,4,5,6,7,8,10,11,12] },
            { className: "text-end", targets: [8] },
        ],

        scrollX: true,
        autoWidth: false,
        pageLength: 50,
        order: [[0, 'desc']],

        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        }
    });

    let lastSearch = '';

    table.on('search.dt', function () {

        let currentSearch = table.search();

        if (currentSearch !== lastSearch) {

            lastSearch = currentSearch;

            loadStats();

        }

    });
    loadStats();

    //table.on('draw.dt', function () {
    //    loadStats();
    //});

    // loadStats();

    // 🔥 EVENTOS DE FILTRO

    // $('#filter_lawyer, #filter_from, #filter_to').change(function(){
    //     table.ajax.reload();
    // });

    // $('#btn-clear-filters').click(function(){
    //     $('#filter_lawyer').val('');
    //     $('#filter_from').val('');
    //     $('#filter_to').val('');

    //     table.ajax.reload();
    // });

    // $('.filter-quick').click(function(){
    //     table.ajax.reload();
    // });

});

$('#workMode .nav-link').on('click', function (e) {

    e.preventDefault();

    $('#workMode .nav-link').removeClass('active');

    $(this).addClass('active');

    workMode = $(this).data('mode');

    if (workMode === 'consultations') {

        $('#pageTitle').html('<i class="bi bi-chat-left-text"></i> Consultas');

        $('#consultationFilters').removeClass('d-none');
        $('#consultationStatusBadges').removeClass('d-none');

        $('#followUpFilters').addClass('d-none');

        $('.btn-new-consultation').removeClass('d-none');

    } else {

        $('#pageTitle').html('<i class="bi bi-telephone"></i> Seguimientos comerciales');

        $('#consultationFilters').addClass('d-none');
        $('#consultationStatusBadges').addClass('d-none');

        $('#followUpFilters').removeClass('d-none');

        $('.btn-new-consultation').addClass('d-none');

    }

    reloadTable();

});

$(document).on('click', '.follow-filter', function () {

    $('.follow-filter').removeClass('active');

    $(this).addClass('active');

    followUpFilter = $(this).data('follow');

    reloadTable();

});


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

$(document).on('click', '.btn-generate-case', function(){

    let url = $(this).data('url');

    if(confirm('¿Desea generar el caso?')){

        $.post(url, {
            _token: '{{ csrf_token() }}'
        }, function(){
            location.reload();
        });

    }

});

// cambio en filtros
$('#filter_lawyer, #filter_service_type, #filter_specialty, #filter_from, #filter_to').change(function(){
    reloadTable();
});

// limpiar filtros
$('#btn-clear-filters').click(function(){
    consultationStatus = '';
    $('#filter_status').val('');
    $('#filter_lawyer').val('');
    $('#filter_from').val('');
    $('#filter_to').val('');
    $('#filter_service_type').val('');
    $('#filter_specialty').val('');

    reloadTable();
});

// botones rápidos
$('.filter-quick').click(function(){

    consultationStatus = $(this).data('status');

    reloadTable();

});

function reloadTable(resetPaging=false){
    table.ajax.reload(function(){
        loadStats();
    }, resetPaging);
}

function loadStats(){

    let searchValue = '';

    if (table) {
        searchValue = table.search();
    }

    $.get("{{ route('consultations.stats') }}", {
        work_mode: workMode,
        status: consultationStatus,
        lawyer_id: $('#filter_lawyer').val(),
        date_from: $('#filter_from').val(),
        date_to: $('#filter_to').val(),
        search: searchValue
    }, function(res){

            console.log(res)
        if (workMode === 'consultations') {

            $('#stat_all').text(res.all);
            $('#stat_new').text(res.new);
            $('#stat_prospect').text(res.prospect);
            $('#stat_accepted').text(res.accepted);
            $('#stat_rejected').text(res.rejected);

        } else {
            $('#stat_follow_all').text(res.all);
            $('#stat_follow_today').text(res.today);
            $('#stat_follow_overdue').text(res.overdue);
            $('#stat_follow_week').text(res.week);
            $('#stat_follow_none').text(res.none);
            $('#stat_follow_accepted').text(res.accepted);
            $('#stat_follow_rejected').text(res.rejected);

        }

    });

}

//loadStats();

// $('#consultationsTable').on('search.dt', function () {
//     loadStats();
// });


$(document).on('click', '.btn-follow-up', function () {

    let consultationId = $(this).data('id');

    $('#followUpForm')[0].reset();

    $('#followUpForm input[name="consultation_id"]').val(consultationId);

    $('#followUpModal').modal('show');

});

$('#followUpForm').submit(function(e){

    e.preventDefault();

    let form=$(this);

    let btn=form.find('button[type="submit"]');

    btn.prop('disabled',true);

    $.ajax({

        url:form.attr('action'),

        type:'POST',

        data:form.serialize(),

        success:function(response){

            btn.prop('disabled',false);

            $('#followUpModal').modal('hide');

            reloadTable(false);

            toastr.success(response.message);

        },

        error:function(xhr){

            if(xhr.status===422){

                // luego mostraremos errores

            }else{

                toastr.error('Ocurrió un error.');

            }

        }

    });

});

</script>

@endpush

@push('styles')
<style>
#workMode .nav-link{

    padding: .35rem .85rem;

    font-size:.90rem;

    color:var(--bs-primary);

    border-radius:.4rem;

    transition:all .2s;

}

#workMode .nav-link.active{

    color:#fff !important;

    font-weight:600;

}

#workMode .nav-link:not(.active):hover{

    background:#eef5ff;

}
.quick-filter.active{

    box-shadow:0 .125rem .25rem rgba(0,0,0,.15);

}
</style>
@endpush