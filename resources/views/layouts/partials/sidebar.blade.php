<div class="sidebar bg-white border-end" style="width:260px; min-height:100vh;">
	<div class="p-3">
		<h5 class="text-center fw-semibold text-primary">
            @if( file_exists(public_path("/img/logo.png")) )
                  <img src="/img/logo.png" alt="" height="100px">
            @else
                  ⚖️ {{ config('app.name') }}
            @endif
		</h5>
	</div>
	<ul class="nav flex-column px-2">
		{{-- Dashboard --}}
		<li class="nav-item">
			<a class="nav-link text-dark" href="{{ route('dashboard') }}">
				<i class="bi bi-speedometer2"></i> Dashboard
			</a>
		</li>
		{{-- CLIENTES --}}
		@role('Administrador|Recepcionista')
		<li class="nav-item">
			<a class="nav-link text-dark" href="{{ route('clients.index') }}">
				<i class="bi bi-people"></i> Clientes
			</a>
		</li>
		@endrole
		{{-- CONSULTAS --}}
		<li class="nav-item">
			<a class="nav-link text-dark" href="{{ route('consultations.index') }}">
				<i class="bi bi-chat-left-text"></i> Consultas
			</a>
		</li>
		{{-- CASOS --}}
		<li class="nav-item">
			<a class="nav-link text-dark" href="{{ route('cases.index') }}">
				<i class="bi bi-folder"></i> Casos
			</a>
		</li>
		{{-- AGENDA --}}
		<!-- <li class="nav-item">
			<a class="nav-link text-dark" href="#">
				<i class="bi bi-calendar-event"></i> Agenda
			</a> -->
		</li>
		{{-- ADMINISTRACIÓN --}}
		@role('Administrador')
		<li class="nav-item mt-1">
			<li class="nav-item">
			    <a class="nav-link text-dark" href="{{ route('legal-specialties.index') }}">
			        <i class="bi bi-tags"></i> Especialidades
			    </a>
			</li>

			<a class="nav-link text-dark d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#menuReportes" role="button">
				<span>
					<i class="bi bi-bar-chart"></i>
					Reportes
				</span>
				<i class="bi bi-chevron-down"></i>
			</a>

		    <div class="collapse" id="menuReportes">
		        <ul class="nav flex-column ms-3">
		            {{-- 1. FINANCIERO INTEGRAL (Financiero + Caja + Rentabilidad) --}}
		            <li class="nav-item">
		                <a class="nav-link text-dark fw-semibold" href="{{ route('reports.financial') }}">
		                    <i class="bi bi-graph-up-arrow"></i>
		                    Financiero Integral
		                </a>
		            </li>
		            <li class="nav-item ms-3">
		                <a class="nav-link text-dark" href="{{ route('reports.cash') }}">
		                    <i class="bi bi-cash-coin"></i>
		                    Caja
		                </a>
		            </li>
		            <li class="nav-item ms-3">
		                <a class="nav-link text-dark" href="{{ route('reports.profitability') }}">
		                    <i class="bi bi-piggy-bank"></i>
		                    Rentabilidad
		                </a>
		            </li>

		            {{-- 2. CLIENTES Y COBRANZA (Clientes + Cobranza) --}}
		            <li class="nav-item mt-1">
		                <a class="nav-link text-dark fw-semibold" href="{{ route('reports.clients') }}">
		                    <i class="bi bi-person-vcard"></i>
		                    Clientes y Cobranza
		                </a>
		            </li>
		            <li class="nav-item ms-3">
		                <a class="nav-link text-dark" href="{{ route('reports.collection') }}">
		                    <i class="bi bi-cash-stack"></i>
		                    Detalle de Cobranza
		                </a>
		            </li>

		            {{-- 3. ACTIVIDAD Y RECURSOS (Operativo + Abogados + Agenda) --}}
		            <li class="nav-item mt-1">
		                <a class="nav-link text-dark fw-semibold" href="{{ route('reports.operational') }}">
		                    <i class="bi bi-briefcase"></i>
		                    Actividad y Recursos
		                </a>
		            </li>
		            <li class="nav-item ms-3">
		                <a class="nav-link text-dark" href="{{ route('reports.lawyers') }}">
		                    <i class="bi bi-person-workspace"></i>
		                    Abogados
		                </a>
		            </li>
		            <li class="nav-item ms-3">
		                <a class="nav-link text-dark" href="{{ route('reports.agenda') }}">
		                    <i class="bi bi-calendar-event"></i>
		                    Agenda
		                </a>
		            </li>

		            {{-- 4. CONVERSIÓN COMERCIAL --}}
		            <li class="nav-item mt-1">
		                <a class="nav-link text-dark fw-semibold" href="{{ route('reports.conversion') }}">
		                    <i class="bi bi-graph-up"></i>
		                    Conversión Comercial
		                </a>
		            </li>
		        </ul>
		    </div>
		</li>


		<li class="nav-item mt-1">
		    <a class="nav-link text-dark d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#menuAdministracion" role="button">
		        <span>
		            <i class="bi bi-gear"></i>
		            Administración
		        </span>
		        <i class="bi bi-chevron-down"></i>
		    </a>
		    <div class="collapse" id="menuAdministracion">
		        <ul class="nav flex-column ms-3">
		            <li class="nav-item">
		                <a class="nav-link text-dark" href="{{ route('users.index') }}">
		                    <i class="bi bi-person"></i>
		                    Usuarios
		                </a>
		            </li>
		            <li class="nav-item">
		                <a class="nav-link text-dark" href="{{ route('roles.index') }}">
		                    <i class="bi bi-shield-lock"></i>
		                    Roles
		                </a>
		            </li>
		            <li class="nav-item">
		                <a class="nav-link text-dark" href="{{ route('permissions.index') }}">
		                    <i class="bi bi-key"></i>
		                    Permisos
		                </a>
		            </li>
		            <li class="nav-item">
		                <a class="nav-link text-dark" href="{{ route('notification-settings.index') }}">
		                    <i class="bi bi-bell"></i>
		                    Notificaciones
		                </a>
		            </li>
		            <li class="nav-item">
		                <a class="nav-link text-dark" href="{{ route('audits.index') }}">
		                    <i class="bi bi-shield-check"></i>
		                    Auditoría
		                </a>
		            </li>
		        </ul>
		    </div>
		</li>
		@endrole
	</ul>
</div>