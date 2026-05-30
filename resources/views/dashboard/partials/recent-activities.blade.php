<div class="card shadow-sm">

    <div class="card-header">

        <h5 class="mb-0">

            Actividad Reciente

        </h5>

    </div>

    <div class="card-body">

        @forelse($recentActivities as $activity)

            <div class="border-bottom py-2">

                <strong>

                    {{ $activity->title }}

                </strong>

                <br>

                <small class="text-muted">

                    {{ optional($activity->case)->title }}

                </small>

            </div>

        @empty

            <p class="text-muted mb-0">

                No hay actividad reciente.

            </p>

        @endforelse

    </div>

</div>