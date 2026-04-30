<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm">
	<div class="container-fluid">
		{{-- Toggle sidebar (futuro JS) --}}
		<button id="toggleSidebar"class="btn btn-outline-secondary me-2"> <i class="bi bi-list"></i> </button>
		{{-- Nombre sistema --}}
		<span class="navbar-brand fw-semibold">
		⚖️ {{ config('app.name') }}
		</span>
		{{-- Usuario --}}
		<div class="ms-auto">
			<div class="dropdown">
				<button class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
					<i class="bi bi-person-circle"></i>
					{{ auth()->user()->name }}
				</button>
				<ul class="dropdown-menu dropdown-menu-end">
					<li>
						<a class="dropdown-item" href="{{ route('profile.edit') }}">
							<i class="bi bi-person"></i>
							Perfil
						</a>
					</li>
					<li>
						<hr class="dropdown-divider">
					</li>
					<li>
						<form method="POST"
						action="{{ route('logout') }}">
							@csrf
							<button class="dropdown-item">
								<i class="bi bi-box-arrow-right"></i>
								Cerrar sesión
							</button>
						</form>
					</li>
				</ul>
		</div>
		</div>
	</div>
</nav>