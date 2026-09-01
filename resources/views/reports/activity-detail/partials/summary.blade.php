<div class="row g-3 mb-4">

    <div class="col-md-3">

        <div class="card text-bg-primary h-100">

            <div class="card-body py-3">

                <div class="d-flex justify-content-between align-items-center">

                    <div>

                        <div class="small text-uppercase text-white-50">

                            Total actividades

                        </div>

                        <div class="fs-3 fw-bold" id="kpiTotal">

                            0

                        </div>

                    </div>

                    <i class="bi bi-list-check fs-2 text-white-50"></i>

                </div>

            </div>

        </div>

    </div>

    @foreach($activityTypes as $key => $label)

        @php

            $color = match($key){

                'legal' => 'primary',

                'judicial_progress' => 'success',

                'communication' => 'info',

                'note' => 'secondary',

                default => 'dark'

            };

        @endphp

        <div class="col-md-3">

            <div class="card h-100 border-0 shadow-sm">

                <div class="card-body py-3">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <div class="small text-uppercase text-muted">

                                {{ $label }}

                            </div>

                            <div class="fs-3 fw-bold" id="kpiType_{{ $key }}">

                                0

                            </div>

                        </div>

                        <span class="badge bg-{{ $color }} text-white">

                            <i class="bi bi-circle-fill"></i>

                        </span>

                    </div>

                </div>

            </div>

        </div>

    @endforeach

</div>
