@include('cases.partials.activities.list')

@include('cases.partials.activities.modal')

@push('scripts')
    @include('cases.partials.activities.scripts')
@endpush

@push('styles')
<style>
.table td,
.table th{

    vertical-align: middle;

}

.activity-title{

    cursor:pointer;

    user-select:none;

    font-weight:600;

}

.activity-title:hover{

    color:var(--bs-primary);

}

.activity-description{

    margin-top:.25rem;

    padding-top:.0;

    color:#6c757d;

    line-height:1.45;

}

#activitiesTable tbody tr{

    transition:background .15s;

}

#activitiesTable tbody tr:hover{

    background:#f8f9fa;

}

/* =====================================================
   CELULAR
===================================================== */

@media (max-width: 768px){

    #activitiesTable thead{

        display:none;

    }

    #activitiesTable,
    #activitiesTable tbody,
    #activitiesTable tr,
    #activitiesTable td{

        display:block;

        width:100%;

    }

    #activitiesTable tr{

        border:1px solid #dee2e6;

        border-radius:.6rem;

        margin-bottom:1rem;

        background:#fff;

        overflow:hidden;

        box-shadow:0 .125rem .25rem rgba(0,0,0,.05);

    }

    #activitiesTable td{

        border:none;

        border-bottom:1px solid #f1f1f1;

        padding:.70rem 1rem;

        text-align:left !important;

    }

    #activitiesTable td:last-child{

        border-bottom:none;

    }

    #activitiesTable td::before{

        content:attr(data-label);

        display:block;

        font-size:.72rem;

        font-weight:700;

        color:#6c757d;

        text-transform:uppercase;

        margin-bottom:.25rem;

    }

    #activitiesTable .btn-group{

        width:100%;

    }

    #activitiesTable .btn{

        flex:1;

    }

}
.activity-item.type-legal {
    border-left: 4px solid var(--bs-primary);
}

.activity-item.type-judicial-progress {
    border-left: 4px solid var(--bs-success);
}

.activity-item.type-communication {
    border-left: 4px solid var(--bs-info);
}

.activity-item.type-note {
    border-left: 4px solid var(--bs-secondary);
}
</style>
@endpush