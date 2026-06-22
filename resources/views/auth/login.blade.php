@extends('layouts.guest')

@section('content')

<h5 class="text-center mb-4">
    Iniciar sesión
</h5>

<form method="POST" action="{{ route('login') }}" id="loginForm">
    @csrf
    {{-- Email --}}
    <x-form.input name="email" type="email" label="Correo electrónico" required autofocus/>
    {{-- Password --}}
    <x-form.input name="password" type="password" label="Contraseña" togglePassword="true" required/>
    {{-- Remember --}}
    <div class="form-check mt-2 mb-2">
        <input type="checkbox" name="remember" class="form-check-input" id="remember">
        <label class="form-check-label" for="remember"> Recordarme </label>
    </div>
    {{-- Button --}}
    <x-form.button class="btn btn-outline-primary w-100">
    <i class="bi bi-box-arrow-in-right"></i> Ingresar
    </x-form.button>
    {{-- Forgot password --}}
    @if (Route::has('password.request'))
    <div class="text-center mt-3">
        <a href="{{ route('password.request') }}" class="text-decoration-none">
            ¿Olvidaste tu contraseña?
        </a>
    </div>
    @endif
</form>

@endsection

@push('scripts')

<script>
$('#loginForm').on('submit', function (e) {
    e.preventDefault();
    // Obtener nuevo token antes de enviar
    $.get("{{ route('refresh.csrf') }}", function (data) {
        $('input[name="_token"]').val(data.token);
        $('#loginForm')[0].submit();
    });
});

$(document).on('click', '.toggle-password', function () {
    let button = $(this);
    let input = button.closest('.input-group').find('input');

    if (input.attr('type') === 'password') {

        input.attr('type', 'text');

        button.html('<i class="bi bi-eye-slash"></i>');

    } else {

        input.attr('type', 'password');

        button.html('<i class="bi bi-eye"></i>');
    }

});
</script>

@endpush