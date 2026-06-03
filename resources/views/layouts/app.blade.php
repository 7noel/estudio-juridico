<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1">

<title>
    {{ config('app.name') }}
</title>

<!-- Bootstrap 5 -->

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons -->

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<!-- jQuery UI -->

<link href="https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css" rel="stylesheet">

<!-- DataTables Bootstrap 5 -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">

<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">

<!-- FullCalendar -->

<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">

<!-- Dropzone -->

<link href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" rel="stylesheet">

<style>

.ui-autocomplete {

max-height: 300px;
overflow-y: auto;
overflow-x: hidden;
width: auto !important;
min-width: 350px;

border-radius: 4px;
border: 1px solid #dee2e6;

background: white;

font-size: 12px;

padding: 0;

z-index: 9999;

}

/* Items */

.ui-menu-item {

padding: 4px 8px;

border-bottom: 1px solid #f1f1f1;
font-size: 12px;

cursor: pointer;

line-height: 1.2;

}

/* Hover */

.ui-menu-item:hover {

background-color: #f8f9fa;

}

/* Seleccionado */

.ui-state-active {

background-color: #0d6efd !important;
color: white !important;

}

/* Sidebar base */
.sidebar {
    width: 260px;
    transition: all 0.3s;
}

/* Sidebar colapsado (PC) */
.sidebar.collapsed {
    margin-left: -260px;
}

/* Modo móvil */
@media (max-width: 768px) {

    .sidebar {
        position: fixed;
        top: 0;
        left: 0;

        height: 100vh;

        z-index: 1040;

        margin-left: -260px;
    }

    .sidebar.show {
        margin-left: 0;
    }

}

#toggleSidebar {
    position: relative;
    z-index: 1050;
}

.content-wrapper {
    min-width: 0;
}

</style>

@stack('styles')

</head>

<body>

<div class="d-flex">

    {{-- SIDEBAR --}}

    @include('layouts.partials.sidebar')

    {{-- CONTENT AREA --}}

    <div class="flex-grow-1 content-wrapper">

        {{-- NAVBAR --}}

        @include('layouts.partials.navbar')

        {{-- ALERTS --}}

        <div class="container-fluid mt-3">

            @include('layouts.partials.alerts')

            @yield('content')

        </div>

    </div>

</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- jQuery UI -->
<script src="https://code.jquery.com/ui/1.13.3/jquery-ui.min.js"></script>

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<!-- Bootstrap 5 integration -->
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<!-- FullCalendar -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

<!-- Dropzone -->
<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>

<!-- APEXCHARTS -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
$(function () {
  // Bloquea reenvíos múltiples
  $(document).on('submit', 'form.form-loading', function (e) {
    var form  = this;
    var $form = $(form);

    // Si ya fue enviado, cancela
    if ($form.data('submitted') === true) {
      e.preventDefault();
      return false;
    }

    // Respeta validación nativa HTML5
    if (form.checkValidity && !form.checkValidity()) {
      return true; // el navegador mostrará los errores
    }

    // Marca como enviado
    $form.data('submitted', true);

    // Deshabilita botones submit y muestra "Enviando…"
    $form.find('button[type="submit"], input[type="submit"]').each(function () {
      var $btn = $(this);

      if ($btn.is('button')) {
        $btn.data('orig-html', $btn.html());
        $btn.html('<span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span> Enviando…');
      } else {
        $btn.data('orig-val', $btn.val());
        $btn.val('Enviando…');
      }
      $btn.prop('disabled', true).addClass('disabled');
    });

    // Evita que se dispare otro submit por Enter mientras navega
    $form.on('keydown.preventResubmit', function (ev) {
      if (ev.key === 'Enter') ev.preventDefault();
    });

    return true; // deja continuar el submit normal (recarga de página)
  });

  // Si el usuario vuelve con Back/forward cache, restablece el estado
  window.addEventListener('pageshow', function (evt) {
    if (evt.persisted) {
      $('form.form-loading').each(function () {
        var $form = $(this).data('submitted', false).off('keydown.preventResubmit');
        $form.find('button[type="submit"], input[type="submit"]').each(function () {
          var $btn = $(this).prop('disabled', false).removeClass('disabled');
          if ($btn.is('button')) {
            var h = $btn.data('orig-html'); if (h != null) $btn.html(h);
          } else {
            var v = $btn.data('orig-val');  if (v != null) $btn.val(v);
          }
        });
      });
    }
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

$(function(){

    $('#toggleSidebar').on('click', function(){

        if (window.innerWidth < 768) {

            // móvil
            $('.sidebar').toggleClass('show');

        } else {

            // desktop
            $('.sidebar').toggleClass('collapsed');

        }

        setTimeout(function(){

            if ($.fn.DataTable) {

                $.fn.dataTable
                    .tables({ visible: true, api: true })
                    .columns.adjust();

            }

        }, 300);

    });

    $(document).on('click', function(e){
        if (window.innerWidth < 768) {
            if (!$(e.target).closest('.sidebar, #toggleSidebar').length) {
                $('.sidebar').removeClass('show');
            }
        }
    });

});

var userLoggedIn = {{ auth()->check() ? 'true' : 'false' }};

function checkSession() {
    if (!userLoggedIn) return;
    return $.ajax({
        url: "{{ route('session.check') }}",
        type: "GET",
        cache: false
    })
    .done(function (data) {
        if (!data.active) {
            $('#sessionExpiredModal').modal('show');
            setTimeout(function () {
                window.location.href = "{{ route('login') }}";
            }, 5000);
        }
    });
}

setInterval(function () {
    checkSession();
}, 300000);

document.addEventListener("visibilitychange", function () {
    if (!document.hidden) {
        checkSession();
    }
});
$(document).on('input', '.text-uppercase', function(){
    this.value = this.value.toUpperCase();
});

function getLocalDateTime() {
    let now = new Date();
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    return now.toISOString().slice(0,16);
}

</script>

@stack('scripts')

</body>

</html>
