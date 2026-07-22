<div class="card mt-3 shadow-sm border-0">

    @php

        $activities = $case->activities->sortByDesc('activity_at');

        $counts = [];

        foreach(config('options.activity_main_types') as $key => $label){

            $counts[$key] = $activities->where('type',$key)->count();

        }

    @endphp

    {{-- Barra superior --}}
    <div class="card-body border-bottom">

        <div class="row align-items-end g-3">

            <div class="col">

                <label class="form-label fw-semibold mb-2">

                    Filtrar actividades por tipo

                </label>

                <div
                    class="d-flex flex-wrap gap-2"
                    id="activityFilters">

                    <button
                        type="button"
                        class="btn btn-sm btn-primary active"
                        data-filter="all">

                        Todas

                        <span class="badge bg-light text-dark ms-1">

                            {{ $activities->count() }}

                        </span>

                    </button>

                    @foreach(config('options.activity_main_types') as $key => $label)

                        <button
                            type="button"
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

                <div class="col-auto">

                    <button
                        class="btn btn-sm btn-outline-primary"
                        id="btnAddActivity"
                        data-bs-toggle="modal"
                        data-bs-target="#modalActivity">

                        <i class="bi bi-plus-lg"></i>

                        Nueva actividad

                    </button>

                </div>

            @endif

        </div>

    </div>

    {{-- Tabla --}}
    <div class="table-responsive">

        <table
            class="table table-hover table-sm align-middle mb-0"
            id="activitiesTable">

            <thead class="table-light">

                <tr>

                    <th style="width:200px" class="text-center">
                        Fecha
                    </th>

                    <th style="width:170px" class="text-center">
                        Tipo
                    </th>

                    <th style="width:220px" class="text-center">
                        SubTipo
                    </th>

                    <th class="text-center">
                        Actividad
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

            <tbody id="activities-list">
                @forelse($activities as $act)

                    @php

                        $type = config("options.activity_main_types.{$act->type}");

                        $subtype =
                            config("options.activity_types.{$act->subtype}")
                            ?? config("options.communication_types.{$act->subtype}")
                            ?? config("options.judicial_progress_types.{$act->subtype}")
                            ?? '-';


                        $color = match($act->type){

                            'legal' => 'primary',

                            'judicial_progress' => 'success',

                            'communication' => 'info',

                            'note' => 'secondary',

                            default => 'dark'

                        };

                    @endphp

                    <tr
                        class="activity-item type-{{ str_replace('_', '-', $act->type) }}" data-type="{{ $act->type }}">

                        {{-- FECHA --}}
                        <td data-label="Fecha">

                            <span class="fw-semibold">
                                {{ $act->activity_at?->translatedFormat('d M Y') }}
                                <small class="text-muted fw-normal">
                                    · {{ $act->activity_at?->format('g:i A') }}
                                </small>
                            </span>

                        </td>

                        {{-- TIPO --}}
                        <td data-label="Tipo">
                                {{ $type }}
                        </td>

                        {{-- SUBTIPO --}}
                        <td data-label="Actuación">
                                {{ $subtype }}

                        </td>

                        {{-- ACTIVIDAD --}}
                        <td data-label="Actividad">

                            <div
                                class="activity-title d-flex align-items-center gap-2">

                                @if($act->description)

                                    <i
                                        class="bi bi-caret-right-fill activity-arrow small text-secondary">
                                    </i>

                                @endif

                                <span>

                                    {{ $act->title ?: '-' }}

                                </span>

                            </div>

                            @if($act->description)

                                <div
                                    class="activity-description" style="display:none;">

                                    {{ $act->description }}

                                </div>

                            @endif

                        </td>

                        {{-- ACCIONES --}}
                        @if($canManageCaseContent)

                            <td
                                data-label="Acciones"
                                class="text-end">

                                <div class="d-flex justify-content-end gap-2">

                                    <button

                                        class="btn btn-sm btn-outline-primary btn-edit-activity"

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

                                        class="btn btn-sm btn-outline-danger btn-delete-activity"

                                        data-id="{{ $act->id }}">

                                        <i class="bi bi-trash"></i> Eliminar

                                    </button>

                                </div>

                            </td>

                        @endif

                    </tr>

                @empty

                <tr>

                    <td
                        colspan="{{ $canManageCaseContent ? 5 : 4 }}"
                        class="text-center py-5">

                        <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>

                        <h6 class="text-muted mb-1">

                            No hay actividades registradas

                        </h6>

                        <small class="text-muted">

                            Aún no se ha registrado ninguna actividad para este expediente.

                        </small>

                    </td>

                </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>
