@include('cases.partials.activities.list')

@include('cases.partials.activities.modal')

@push('scripts')
    @include('cases.partials.activities.scripts')
@endpush

@push('styles')
<style>
.activity-item{

    border-bottom:1px solid #edf1f5;

    position:relative;

    transition:.20s;

}

.activity-item:last-child{

    border-bottom:none;

}

.activity-item:hover{

    background:#f8fbff;

}

.activity-item::before{

    content:"";

    position:absolute;

    left:0;

    top:0;

    bottom:0;

    width:4px;

    border-radius:0 3px 3px 0;

}

.activity-legal::before{

    background:#0d6efd;

}

.activity-communication::before{

    background:#0dcaf0;

}

.activity-judicial_progress::before{

    background:#198754;

}

.activity-note::before{

    background:#6c757d;

}

.activity-title:hover{

    color:#0d6efd;

}

.activity-description{

    display:block;

    margin-top:12px;

    padding-top:12px;

    border-top:1px dashed #d8dde3;

    color:#5f6b78;

    line-height:1.6;

    font-size:.90rem;

}

.activity-subtype{

    margin-top:6px;

    font-size:.82rem;

    color:#7a8694;

}

.activity-subtype i{

    margin-right:4px;

}

.activity-row{

    display:grid;

    grid-template-columns:

        150px
        220px
        1fr
        220px;

    align-items:center;

    gap:16px;

    padding:14px 20px;

}

.activity-date{

    white-space:nowrap;

    font-weight:600;

    font-size:.92rem;

}

.activity-date span{

    color:#6c757d;

    margin-left:4px;

    font-weight:400;

}

.activity-date strong{

    font-size:.95rem;

}

.activity-date small{

    color:#6c757d;

}

.activity-content{

    min-width:0;

}

.activity-title{

    display:flex;

    align-items:center;

    gap:8px;

    font-weight:600;

    font-size:.96rem;

    color:#27313d;

    cursor:pointer;

    user-select:none;

    transition:.2s;

}

.activity-title-text{

    white-space:nowrap;

    overflow:hidden;

    text-overflow:ellipsis;

}

.activity-meta{

    display:flex;

    align-items:center;

    gap:8px;

    font-weight:500;

    color:#495057;

}

.activity-actions{

    display:flex;

    justify-content:flex-end;

    gap:8px;

}

.activity-item{

    overflow:hidden;

}

.activity-title-text{

    flex:1;

    overflow:hidden;

    white-space:nowrap;

    text-overflow:ellipsis;

}

.activity-meta{

    justify-content:center;

}

.activity-actions .btn{

    min-width:90px;

}

</style>
@endpush