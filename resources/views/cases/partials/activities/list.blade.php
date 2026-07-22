<div class="card mt-3 shadow-sm border-0">

    @php

        $activities = $case->activities->sortByDesc('activity_at');

        $counts = [];

        foreach(config('options.activity_main_types') as $key => $label){

            $counts[$key] = $activities->where('type',$key)->count();

        }

    @endphp

    <div class="card-header bg-white d-flex justify-content-between align-items-center">

        <div>

            <h5 class="mb-0">
                <i class="bi bi-list-task text-primary"></i>
                Actividades
            </h5>

            <small class="text-muted">
                Historial cronológico del expediente
            </small>

        </div>

        @if($canManageCaseContent)

            <button
                class="btn btn-primary btn-sm"
                id="btnAddActivity"
                data-bs-toggle="modal"
                data-bs-target="#modalActivity">

                <i class="bi bi-plus-lg"></i>

                Agregar actividad

            </button>

        @endif

    </div>

    <div class="card-body border-bottom">

        <ul
            class="nav nav-pills"
            id="activityFilters">

            <li class="nav-item">

                <button
                    type="button"
                    class="nav-link active"
                    data-filter="all">

                    Todas

                    <span class="badge bg-secondary ms-1">

                        {{ $activities->count() }}

                    </span>

                </button>

            </li>

            @foreach(config('options.activity_main_types') as $key => $label)

                <li class="nav-item">

                    <button
                        type="button"
                        class="nav-link"
                        data-filter="{{ $key }}">

                        {{ $label }}

                        <span class="badge bg-secondary ms-1">

                            {{ $counts[$key] }}

                        </span>

                    </button>

                </li>

            @endforeach

        </ul>

    </div>

    <div class="card-body p-0">

        <div id="activities-list">

            @forelse($activities as $act)

                @php

                    $type = config("options.activity_main_types.{$act->type}");

                    $subtype =
                        config("options.activity_types.{$act->subtype}")
                        ?? config("options.communication_types.{$act->subtype}")
                        ?? config("options.judicial_progress_types.{$act->subtype}")
                        ?? '-';

                    $icon = match($act->type){

                        'legal' => 'bi-bank',

                        'judicial_progress' => 'bi-building',

                        'communication' => 'bi-telephone',

                        'note' => 'bi-journal-text',

                        default => 'bi-circle'

                    };

                    $color = match($act->type){

                        'legal' => 'primary',

                        'judicial_progress' => 'success',

                        'communication' => 'info',

                        'note' => 'secondary',

                        default => 'dark'

                    };

                @endphp

                <div
                    class="activity-item activity-{{ $act->type }}"
                    data-type="{{ $act->type }}">

                    <div class="activity-row">

                        <div class="activity-date">

                            {{ strtoupper($act->activity_at?->translatedFormat('d M')) }}

                            <span class="text-muted">

                                {{ $act->activity_at?->format('H:i') }}

                            </span>

                        </div>

                        <div class="activity-meta">

                            <i class="bi {{ $icon }} text-{{ $color }}"></i>

                            <span>

                                {{ $subtype }}

                            </span>

                        </div>

                        <div class="activity-content">

                            <div class="activity-title">

                                <span class="activity-title-text">

                                    {{ $act->title ?: '-' }}

                                </span>

                            </div>

                            @if($act->description)

                                <div class="activity-description d-none">

                                    {{ $act->description }}

                                </div>

                            @endif

                        </div>

                        {{-- BOTONES --}}
                        <div class="activity-actions">

                            @if($canManageCaseContent)

                                <button

                                    class="btn btn-sm btn-light btn-edit-activity"

                                    title="Editar"

                                    data-id="{{ $act->id }}"
                                    data-type="{{ $act->type }}"
                                    data-subtype="{{ $act->subtype }}"
                                    data-title="{{ $act->title }}"
                                    data-description="{{ $act->description }}"
                                    data-date="{{ $act->activity_at?->format('Y-m-d\TH:i') }}"
                                    data-has-event="{{ $act->agendaEvent ? 1 : 0 }}"

                                    data-agenda-type="{{ $act->agendaEvent?->type }}"
                                    data-agenda-title="{{ $act->agendaEvent?->title }}"
                                    data-agenda-description="{{ $act->agendaEvent?->description }}"
                                    data-agenda-start="{{ $act->agendaEvent?->start_datetime?->format('Y-m-d H:i:s') }}"
                                    data-agenda-end="{{ $act->agendaEvent?->end_datetime?->format('Y-m-d H:i:s') }}"
                                    data-agenda-location="{{ $act->agendaEvent?->location }}">

                                    <i class="bi bi-pencil"></i> Editar

                                </button>

                                <button

                                    class="btn btn-sm btn-light btn-delete-activity"

                                    title="Eliminar"

                                    data-id="{{ $act->id }}">

                                    <i class="bi bi-trash text-danger"></i> Eliminar

                                </button>

                            @endif

                        </div>

                    </div>

                </div>

            @empty

                <div class="text-center py-5">

                    <i class="bi bi-inbox fs-1 text-muted"></i>

                    <h6 class="mt-3 text-muted">
                        Aún no hay actividades registradas.
                    </h6>

                    <p class="text-muted mb-0">
                        Utiliza el botón <strong>"Agregar actividad"</strong> para registrar el primer movimiento del expediente.
                    </p>

                </div>

            @endforelse

        </div> {{-- #activities-list --}}

    </div> {{-- card-body listado --}}

</div> {{-- card --}}