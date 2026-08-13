# Sistema de Gestión para Estudio Jurídico

Sistema web para la gestión integral de un estudio jurídico, construido con **Laravel 12** y **PHP 8.2+**. Permite administrar el ciclo completo de atención legal: registro de clientes, consultas, seguimiento comercial, expedientes/casos, pagos, gastos, agenda y reportes financieros.

---

## ✨ Características

- **Clientes**: Registro con tipos de documento peruanos (DNI, CEX, PAS, RUC), búsqueda por ubigeo y autocompletado.
- **Consultas**: Registro de consultas legales con estados (`Nuevo → Prospecto → Aceptado/Rechazado`), cuotas de pago y seguimiento comercial (llamadas, WhatsApp, correo).
- **Casos (Expedientes)**: Vinculados a consultas, con estados (`Abierto, En proceso, En espera, Culminado`), actividades procesales, documentos adjuntos y gastos asociados.
- **Pagos / Cobranzas**: Pagos por cuota con métodos peruanos (efectivo, transferencia, Yape, Plin, tarjeta) y generación automática de caso al pagar.
- **Gastos**: Registro de gastos por caso con categorías (tasa judicial, movilidad, SUNARP, notaría, peritaje, etc.).
- **Agenda**: Eventos por caso (audiencias, vencimientos, reuniones, tareas) con calendario visual.
- **Especialidades Legales**: Catálogo de especialidades y materias jurídicas.
- **Reportes (8 módulos)**: Caja, Cobranza, Financiero, Rentabilidad, Operativo, Abogados, Clientes y Agenda — con KPIs y gráficos.
- **Notificaciones WhatsApp**: Envío de mensajes vía Evolution API con colas de trabajo y registro de notificaciones.
- **Administración**: Gestión de usuarios, roles y permisos (RBAC).
- **Multi-sede**: Soporte para múltiples establecimientos (sedes) con filtrado por sede.

---

## 🛠️ Requisitos

- PHP 8.2 o superior
- Composer
- Node.js y npm (para assets, si se usan)
- Base de datos: SQLite (por defecto) o MySQL

---

## 📦 Instalación

### 1. Instalar dependencias

```bash
composer install
```

### 2. Crear archivo de entorno

```bash
cp .env.example .env
php artisan key:generate
```

### 3. Configurar la base de datos

Por defecto usa SQLite. Crear el archivo de base de datos:

```bash
touch database/database.sqlite
```

Si usas MySQL, edita `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=estudio_juridico
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Ejecutar migraciones y seeders

```bash
php artisan migrate --seed
```

Los seeders crean:
- Roles: `Administrador`, `Recepcionista`, `Abogado`
- Permisos por módulo (clientes, consultas, casos, agenda, roles, usuarios, establecimientos)
- Especialidades y materias jurídicas
- Ubigeos (departamentos, provincias, distritos)
- Configuraciones de notificación

### 5. Crear enlace de almacenamiento (para documentos)

```bash
php artisan storage:link
```

### 6. (Opcional) Compilar assets

```bash
npm install
npm run build
```

> **Nota**: El sistema carga las librerías frontend (Bootstrap, DataTables, FullCalendar, etc.) por CDN, por lo que los assets npm son opcionales.

---

## ⚙️ Configuración de WhatsApp (Evolution API)

Para habilitar el envío de mensajes de WhatsApp, configurar en `.env`:

```env
EVOLUTION_API_URL=https://tu-servidor-evolution.com
EVOLUTION_API_KEY=tu-api-key
EVOLUTION_INSTANCE=nombre-de-instancia
```

Se recomienda ejecutar el worker de colas para procesar los envíos:

```bash
php artisan queue:listen
```

---

## 👥 Roles y Permisos

| Rol | Acceso |
|-----|--------|
| **Administrador** | Acceso total: clientes, consultas, casos, reportes, agenda, usuarios, roles, permisos, notificaciones |
| **Recepcionista** | Clientes, consultas y agenda |
| **Abogado** | Consultas, casos y agenda (filtrado por abogado asignado) |

---

## 🏗️ Estructura del Proyecto

```
app/
├── Console/          # Comandos artisan
├── Http/
│   ├── Controllers/  # Controladores (incluye Reportes)
│   └── Requests/     # Form Requests de validación
├── Jobs/             # Jobs en cola (envío WhatsApp)
├── Models/           # Modelos Eloquent
├── Policies/         # Políticas de autorización
├── Providers/        # Proveedores de servicios
├── Services/         # Servicios (WhatsAppService)
└── View/             # View Composers
config/
├── options.php       # Catálogos de opciones (estados, tipos, métodos)
└── services.php      # Configuración de servicios externos (Evolution API)
database/
├── migrations/       # Esquema de base de datos
└── seeders/          # Datos iniciales
resources/views/      # Vistas Blade
routes/web.php        # Rutas web
```

---

## 🔑 Cuenta de acceso inicial

Después de ejecutar los seeders, crear un usuario administrador manualmente o mediante Tinker:

```bash
php artisan tinker
```

```php
$user = App\Models\User::create([
    'name' => 'Administrador',
    'email' => 'admin@estudiojuridico.com',
    'password' => bcrypt('password'),
]);

$user->assignRole('Administrador');
```

---

## 🧪 Tests

```bash
php artisan test
```

---

## 📄 Licencia

Proyecto privado.