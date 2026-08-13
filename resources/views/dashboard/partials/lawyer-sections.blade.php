<div class="row mt-4">

    {{-- Prospectos por contactar --}}

    <div class="col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Prospectos por Contactar</h5>
                <a href="{{ route('consultations.index') }}" class="btn btn-sm btn-outline-primary">Ver todas</a>
            </div>
            <div class="card-body">
                @forelse($prospectsToContact as $consultation)
                    <div class="border-bottom py-2">
                        <a href="{{ route('consultations.show', $consultation) }}" class="text-decoration-none">
                            <strong>{{ $consultation->title }}</strong>
                        </a>
                        <br>
                        <small class="text-muted">
                            {{ optional($consultation->client)->full_name }}
                            @if($consultation->next_follow_up_at)
                                · Próximo: {{ $consultation->next_follow_up_at->format('d/m/Y') }}
                            @endif
                        </small>
                    </div>
                @empty
                    <p class="text-muted mb-0">No hay prospectos pendientes de contacto.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Próximos vencimientos --}}

    <div class="col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-header">
                <h5 class="mb-0">Próximos Vencimientos</h5>
            </div>
            <div class="card-body">
                @forelse($upcomingDeadlinesList as $event)
                    <div class="border-bottom py-2">
                        <a href="{{ route('cases.show', $event->case_id) }}" class="text-decoration-none">
                            <strong>{{ $event->title }}</strong>
                        </a>
                        <br>
                        <small class="text-muted">
                            {{ optional($event->case)->title }}
                            · {{ $event->start_datetime->format('d/m/Y H:i') }}
                        </small>
                    </div>
                @empty
                    <p class="text-muted mb-0">No hay vencimientos en los próximos 7 días.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Casos sin actividad --}}

    <div class="col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-header">
                <h5 class="mb-0">Casos Sin Actividad</h5>
            </div>
            <div class="card-body">
                @forelse($inactiveCasesList as $case)
                    <div class="border-bottom py-2">
                        <a href="{{ route('cases.show', $case) }}" class="text-decoration-none">
                            <strong>{{ $case->title }}</strong>
                        </a>
                        <br>
                        <small class="text-muted">
                            {{ optional($case->client)->full_name }}
                            · Abierto: {{ optional($case->opened_at)->format('d/m/Y') }}
                        </small>
                    </div>
                @empty
                    <p class="text-muted mb-0">No hay casos sin actividad reciente.</p>
                @endforelse
            </div>
        </div>
    </div>

</div>