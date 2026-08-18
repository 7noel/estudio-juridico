# Manual de Usuario
## Sistema de Gestión del Estudio Jurídico

---

## Tabla de Contenidos

1. [Introducción](#introducción)
2. [Visión general del flujo de trabajo](#visión-general-del-flujo-de-trabajo)
3. [Capítulo 1: Recepción / Secretaría](#capítulo-1-recepción--secretaría)
   - [Registrar un cliente](#11-registrar-un-cliente)
   - [Crear una consulta](#12-crear-una-consulta)
   - [Registrar un pago](#13-registrar-un-pago)
   - [Seguimiento de prospectos](#14-seguimiento-de-prospectos)
4. [Capítulo 2: Abogado / Asociado](#capítulo-2-abogado--asociado)
   - [Revisar y completar una consulta](#21-revisar-y-completar-una-consulta)
   - [Generar un caso](#22-generar-un-caso)
   - [Iniciar el caso](#23-iniciar-el-caso)
   - [Registrar actividades](#24-registrar-actividades)
   - [Subir documentos](#25-subir-documentos)
   - [Registrar gastos](#26-registrar-gastos)
   - [Gestionar la agenda](#27-gestionar-la-agenda)
   - [Pausar, reanudar o cerrar el caso](#28-pausar-reanudar-o-cerrar-el-caso)
5. [Capítulo 3: Administrador / Dueño del Estudio](#capítulo-3-administrador--dueño-del-estudio)
   - [Panel principal (Dashboard)](#31-panel-principal-dashboard)
   - [Reportes](#32-reportes)
   - [Administración de usuarios, roles y permisos](#33-administración-de-usuarios-roles-y-permisos)
   - [Auditoría del sistema](#34-auditoría-del-sistema)
6. [Gestión de Notificaciones](#gestión-de-notificaciones)
7. [Solución de Problemas Comunes (FAQ)](#solución-de-problemas-comunes-faq)
8. [Glosario rápido](#glosario-rápido)

---

## Introducción

**Este sistema ha sido diseñado para darle trazabilidad total a cada cliente que entra por la puerta del estudio. Desde el primer llamado (consulta) hasta el cierre del expediente (caso), cada acción queda registrada para que usted, como abogado o administrador, pueda medir su tiempo, sus costos y su productividad sin necesidad de depender del papel o de la memoria.**

Este manual le explicará, paso a paso, cómo utilizar cada módulo del sistema según el rol que usted desempeña dentro del estudio. Está organizado en tres grandes capítulos, uno para cada perfil de usuario:

- **Recepción / Secretaría**: la persona que recibe al cliente, crea la consulta, registra los pagos y realiza el seguimiento de los prospectos.
- **Abogado / Asociado**: el profesional que evalúa la consulta, genera el caso, realiza el trabajo legal y deja registro de cada actividad, documento, gasto y evento de agenda.
- **Administrador / Dueño del Estudio**: la persona que supervisa la productividad del equipo y las finanzas del estudio a través de los reportes, y que administra los usuarios y las configuraciones del sistema.

> **Consejo:** Antes de comenzar a trabajar, asegúrese de que sus datos de acceso hayan sido creados por el administrador y de que su número de celular esté correctamente registrado. Esto es indispensable para recibir las notificaciones del sistema.

---

## Visión general del flujo de trabajo

El ciclo de vida de un cliente dentro del sistema sigue un orden lógico. Le presentamos un resumen del flujo completo para que usted comprenda dónde encaja cada tarea:

```
Cliente llega al estudio
        │
        ▼
Recepción crea la CONSULTA (datos básicos, sin monto ni cuotas)
        │
        ▼
Se informa al ABOGADO seleccionado
        │
        ▼
El abogado revisa y edita la consulta (detalles, monto, cuotas)
        │
        ├──► Si el cliente aún no decide: la consulta queda en estado PROSPECTO
        │         └── Recepción realiza SEGUIMIENTO (llamadas) hasta que decida
        │
        ├──► Si el cliente acepta: se GENERA EL CASO
        │         │
        │         ▼
        │         El abogado INICIA EL CASO
        │         │
        │         ▼
        │         Registra ACTIVIDADES, DOCUMENTOS, GASTOS y AGENDA
        │         │
        │         ▼
        │         El abogado CIERRA EL CASO al finalizar
        │
        └──► Si el cliente no acepta: la consulta se marca como RECHAZADA
                  │
                  ▼
              Fin del proceso
                        │
                        ▼
        El Administrador revisa los REPORTES
        (productividad, finanzas, cobranza, etc.)
```

**Ideas clave de este flujo:**

- **La consulta** es el primer registro. En esta etapa **no es obligatorio** colocar montos ni cuotas.
- **El seguimiento** es la herramienta de recepción para mantener contacto con los prospectos que todavía no deciden.
- **El caso** nace solo cuando el cliente acepta contratar los servicios. A partir de ahí, el abogado trabaja con actividades, documentos, gastos y agenda.
- **Los pagos** los registra siempre el personal de recepción.
- **Los reportes** permiten al administrador medir productividad, ingresos, egresos y calidad del seguimiento.

---

## Capítulo 1: Recepción / Secretaría

Como personal de recepción, usted es el primer punto de contacto con el cliente. Sus principales responsabilidades en el sistema son:

- Registrar los datos del cliente.
- Crear la consulta inicial.
- Registrar los pagos de las cuotas.
- Realizar el seguimiento de los prospectos.

### 1.1 Registrar un cliente

Para que una consulta pueda ser creada, primero debe existir un cliente registrado en el sistema. Si el cliente ya fue registrado anteriormente, puede omitir este paso y buscarlo directamente al crear la consulta.

**Pasos para registrar un cliente:**

1. Ingrese al módulo **Clientes** desde el menú lateral izquierdo.
2. Haga clic en el botón **Nuevo cliente** (o el botón de agregar que aparece en la parte superior de la lista).
3. Complete los campos solicitados:
   - **Tipo de documento** (DNI, Carnet de Extranjería, Pasaporte, RUC, entre otros).
   - **Número de documento**.
   - **Nombres y apellidos**.
   - **Teléfono / Celular**: registre aquí el número de celular del cliente. Este dato es de suma importancia para las notificaciones automáticas.
   - **Correo electrónico** (si aplica).
   - **Dirección** (si aplica).
4. Haga clic en el botón **Guardar**.

> **Importante:** Asegúrese de que el número de celular del cliente esté correcto, de lo contrario las notificaciones automáticas no llegarán. Verifique siempre el número con el propio cliente antes de guardar.

> **Consejo:** Si el cliente no está registrado y usted se encuentra creando una consulta, el sistema le ofrece la opción de registrar al cliente de forma rápida desde la misma ventana de la consulta, sin necesidad de salir de ella.

### 1.2 Crear una consulta

Cuando un cliente llega al estudio y solicita información o asesoría, usted deberá crear una consulta. **En este momento no es necesario registrar montos ni cuotas**; solo los datos básicos de la consulta. Los montos y las cuotas los podrá agregar luego el abogado encargado.

**Pasos para crear una consulta:**

1. Ingrese al módulo **Consultas** desde el menú lateral izquierdo.
2. Haga clic en el botón **Nueva consulta** (o el botón de agregar que aparece en la parte superior de la lista).
3. Complete los siguientes campos:
   - **Tipo de servicio**: seleccione el tipo (Proceso Judicial, Acto Procesal, Acto Extrajudicial, Conciliación).
   - **Especialidad**: seleccione la especialidad legal correspondiente.
   - **Materia**: seleccione la materia. Este listado se carga automáticamente según la especialidad elegida.
   - **Cliente**: busque y seleccione al cliente. Si no existe, use el botón para **crear cliente rápidamente**.
   - **Abogado**: seleccione el abogado que atenderá la consulta. Al guardar, el sistema le informará automáticamente al abogado.
   - **Título**: escriba un título breve que resuma el motivo de la consulta.
   - **Descripción**: escriba los detalles que el cliente comenta en esta primera visita.
4. Si la consulta es nueva, el sistema le mostrará la opción **Cambiar a Prospecto** marcada por defecto. Esto permite que el personal de recepción realice el seguimiento posterior.
5. Haga clic en el botón **Guardar**.

> **Consejo:** En la sección de **Cuotas** puede dejar todo vacío por ahora. El abogado decidirá más adelante si corresponde registrar montos y cuotas, y en qué condiciones.

### 1.3 Registrar un pago

Los pagos de las cuotas los registra siempre el personal de recepción. Para ello:

**Pasos para registrar un pago:**

1. Ingrese al módulo **Consultas**.
2. Localice la consulta del cliente y haga clic en su número o en el botón **Ver** para abrir el detalle.
3. Dentro de la consulta, ubique la tabla de **Cuotas**. Cada cuota muestra su monto, lo pagado, el saldo y la fecha de vencimiento.
4. Haga clic en el botón **Pagar** correspondiente a la cuota que el cliente va a cancelar.
5. En la ventana que se abre, complete:
   - **Método de pago**: efectivo, transferencia, depósito, Yape, Plin o tarjeta.
   - **Monto**: indique el monto que recibe. Si el cliente paga solo una parte, el sistema lo registrará como pago parcial.
6. Haga clic en el botón **Guardar pago** (o **Registrar**).

Al guardar, el sistema actualiza automáticamente el resumen financiero de la consulta: **Total**, **Pagado**, **Pendiente** y el **Estado financiero** (por ejemplo: al día, pendiente o vencido).

> **Importante:** Si el cliente paga parcialmente una cuota, esta quedará con estado **Parcial**. El saldo restante continuará pendiente hasta que se complete.
>
> **Consejo:** Al momento de cobrar, confirme con el cliente el método de pago y registre siempre el monto exacto recibido para que los reportes financieros del estudio reflejen la realidad.

### 1.4 Seguimiento de prospectos

Cuando una consulta queda en estado **Prospecto**, significa que el cliente aún no decide si contratará los servicios del estudio. Su labor es mantener el contacto con él hasta que tome una decisión. Para ello, el sistema cuenta con la opción de **Seguimientos**.

**Pasos para registrar un seguimiento:**

1. Ingrese a la consulta del prospecto.
2. En la sección **Seguimientos**, haga clic en el botón **Nuevo seguimiento**.
3. Complete la ventana con los siguientes datos:
   - **Fecha del contacto**: la fecha en la que usted llamó o se comunicó con el cliente (viene con la fecha de hoy por defecto).
   - **Tipo de comunicación**: llamada telefónica, WhatsApp, correo electrónico, reunión u otro.
   - **Resultado**: el resultado de la comunicación. Las opciones son:
     - **Acepta contratar**: el cliente aceptó los servicios.
     - **Interesado**: mostró interés pero no confirmó.
     - **Solicita tiempo**: pidió más tiempo para decidir.
     - **Esperando propuesta**: está esperando una propuesta del estudio.
     - **Reunión programada**: se programó una reunión.
     - **No contestó**: no respondió la llamada.
     - **Rechazado**: manifestó que no contratará.
     - **Otro**: cualquier otra situación.
   - **Próximo contacto**: la fecha en la que debe volver a llamar al cliente. Este campo es opcional pero muy recomendable.
   - **Observaciones**: escriba un resumen de lo conversado.
4. Haga clic en el botón **Guardar seguimiento**.

**Comportamiento especial según el resultado:**

- Si el resultado es **Acepta contratar**, el sistema le mostrará la opción **Generar caso al guardar** (marcada por defecto). Al guardar el seguimiento, el sistema creará automáticamente el caso para ese cliente.
- Si el resultado es **Rechazado**, el sistema le mostrará la opción **Rechazar consulta al guardar** (marcada por defecto). Al guardar, la consulta pasará a estado **Rechazado** y se dará por terminado el proceso.

> **Importante:** Si la consulta queda en estado **Prospecto**, recepción debe programar el seguimiento en el campo **Próximo contacto**. Así ningún prospecto quedará en el olvido y el administrador podrá verificar que el seguimiento se está realizando correctamente.

> **Consejo:** Registre siempre el resultado real de la comunicación. Los reportes del administrador toman esta información para medir la calidad del seguimiento comercial del estudio.

---

## Capítulo 2: Abogado / Asociado

Como abogado, usted es responsable de evaluar las consultas asignadas, convertir las consultas en casos y ejecutar el trabajo legal dejando evidencia de cada acción en el sistema.

### 2.1 Revisar y completar una consulta

Cuando recepción crea una consulta, usted recibe la notificación correspondiente. Puede revisarla y completar la información que falte.

**Pasos para revisar una consulta:**

1. Ingrese al módulo **Consultas** desde el menú lateral.
2. Localice la consulta asignada a usted y haga clic en su número para abrir el detalle.
3. Revise los datos registrados por recepción: tipo de servicio, especialidad, materia, cliente y la descripción inicial.
4. Si necesita complementar la información, haga clic en el botón **Editar**.
5. Complete los campos adicionales que considere necesarios:
   - **Descripción**: amplíe los detalles del caso del cliente.
   - **Monto total**: si ya hay un acuerdo económico, registre el monto.
   - **Cuotas**: si el pago será fraccionado, registre las cuotas indicando el **monto** de cada una y su **fecha de vencimiento**. El sistema le permite agregar cuotas de forma manual o generarlas automáticamente en partes iguales.
6. Haga clic en el botón **Guardar**.

> **Consejo:** No todas las consultas necesitan monto y cuotas desde el inicio. Usted decide, según el caso, si corresponde registrarlos o si prefiere hacerlo más adelante. Lo importante es que el cliente y la descripción queden bien identificados.

### 2.2 Generar un caso

Cuando el cliente acepta contratar los servicios del estudio, usted debe convertir la consulta en un **caso**. Esto se puede hacer de dos maneras:

**Forma A: Desde la consulta**

1. Abra la consulta del cliente.
2. Haga clic en el botón **Generar caso**.
3. Confirme la acción en la ventana de confirmación.

**Forma B: Desde un seguimiento (cuando recepción registra "Acepta contratar")**

1. El personal de recepción registra un seguimiento con resultado **Acepta contratar**.
2. El sistema crea el caso automáticamente al guardar el seguimiento.
3. Usted verá el nuevo caso en el módulo **Casos**.

Una vez generado, el caso hereda los datos de la consulta (cliente, abogado, especialidad, materia, tipo de servicio) y queda en estado **Abierto**.

> **Importante:** El caso se genera únicamente cuando el cliente acepta. Mientras la consulta esté en estado **Prospecto**, la labor principal es el seguimiento por parte de recepción.

### 2.3 Iniciar el caso

Para poder registrar actividades, documentos, gastos y eventos de agenda, el caso debe estar **en proceso**. Para ello:

**Pasos para iniciar un caso:**

1. Ingrese al módulo **Casos**.
2. Abra el caso haciendo clic en su número.
3. Haga clic en el botón **Iniciar Caso**.
4. Confirme la acción.

El caso pasará a estado **En proceso** y las pestañas de **Actividades**, **Documentos**, **Gastos** y **Agenda** quedarán habilitadas.

> **Consejo:** Recuerde completar los datos del caso antes de iniciarlo, como el **Juzgado** y el **Número de expediente**. Puede hacerlo con el botón **Editar** desde el propio caso.

### 2.4 Registrar actividades

Las actividades son el registro del trabajo diario que usted realiza en el caso. Sirven para dejar constancia de cada avance y para que el administrador pueda medir su productividad.

**Pasos para registrar una actividad:**

1. Abra el caso correspondiente.
2. En la pestaña **Actividades**, haga clic en el botón **Nueva actividad**.
3. Complete la ventana con los siguientes datos:
   - **Tipo de actividad**: seleccione el tipo. Las opciones son:
     - **Actos procesales** (audiencia, presentación de escrito, notificación de resolución, diligencia, otros).
     - **Avance judicial** (juez te escucha, MAU, impulso presencial, CEJ, queja ANC, otros).
     - **Comunicación** (llamada telefónica, WhatsApp, correo electrónico, reunión, otros).
     - **Nota** (apuntes internos del caso).
   - **Subtipo**: seleccione el subtipo correspondiente. El listado se carga automáticamente según el tipo elegido.
   - **Título**: escriba un título breve de la actividad.
   - **Descripción**: detalle lo realizado.
   - **Fecha**: fecha y hora de la actividad.
4. Si lo desea, active la opción **Crear evento en agenda** para que esta actividad quede también registrada en su agenda con su propia fecha, hora y ubicación.
5. Haga clic en el botón **Guardar**.

**Uso del filtro de actividades:**

Dentro de la pestaña **Actividades** encontrará un filtro rápido con los tipos: **Todas**, **Actos procesales**, **Avance judicial**, **Comunicación** y **Nota**. Úselo para ver únicamente las actividades de un tipo específico.

> **Consejo:** Si usted selecciona un filtro (por ejemplo, **Avance judicial**) y luego hace clic en **Nueva actividad**, el sistema preseleccionará automáticamente ese tipo de actividad en la ventana. Solo le faltará completar el subtipo, título y descripción. Esto le ahorrará tiempo.

> **Importante:** Registre cada actividad con su fecha real. Los reportes de productividad del administrador se basan en la información registrada aquí.

### 2.5 Subir documentos

El sistema le permite adjuntar todos los documentos del caso (escritos, resoluciones, contratos, evidencias, etc.).

**Pasos para subir un documento:**

1. Abra el caso correspondiente.
2. Haga clic en la pestaña **Documentos**.
3. Haga clic en el botón **Nuevo documento** (o el botón de subir archivo).
4. Complete la información:
   - **Tipo de documento**: estrategia legal, contrato, resolución judicial, evidencia, escrito judicial, documento de identidad u otro.
   - **Título / descripción**: identifique el documento.
   - **Archivo**: seleccione el archivo desde su computadora.
5. Haga clic en el botón **Guardar** (o **Subir**).

El archivo quedará disponible dentro del caso para su consulta en cualquier momento. También puede **editar** la información del documento o **eliminarlo** si ya no corresponde.

> **Consejo:** Use nombres descriptivos para los archivos y seleccione el tipo de documento correcto. Esto facilitará la búsqueda cuando el caso tenga muchos documentos.

### 2.6 Registrar gastos

Los gastos son los desembolsos que el estudio realiza por cuenta del caso (tasas judiciales, movilidad, copias, notaría, peritajes, etc.). Registrar los gastos permite al administrador conocer cuánto cuesta realmente cada caso y la rentabilidad del estudio.

**Pasos para registrar un gasto:**

1. Abra el caso correspondiente.
2. Haga clic en la pestaña **Gastos**.
3. Haga clic en el botón **Nuevo gasto**.
4. Complete la información:
   - **Categoría**: tasa judicial, movilidad, copias e impresiones, SUNARP, notaría, peritaje, viáticos, gastos de oficina, servicios, marketing, impuestos u otros.
   - **Descripción**: detalle en qué consistió el gasto.
   - **Monto**: indique el monto exacto.
   - **Fecha**: fecha en la que se realizó el gasto.
5. Haga clic en el botón **Guardar**.

> **Consejo:** Registre los gastos apenas se realicen. Si deja de hacerlo, los reportes financieros no reflejarán el costo real de cada caso y la rentabilidad calculada será incorrecta.

### 2.7 Gestionar la agenda

La agenda le permite organizar sus audiencias, reuniones, vencimientos, llamadas y tareas. Puede registrar eventos desde dos lugares:

**Opción A: Desde el caso (evento relacionado al expediente)**

1. Abra el caso correspondiente.
2. Haga clic en la pestaña **Agenda**.
3. Haga clic en el botón **Nuevo evento**.
4. Complete:
   - **Tipo de evento**: audiencia, vencimiento, reunión, tarea, llamada u otro.
   - **Título**: nombre del evento.
   - **Descripción**: detalles.
   - **Fecha y hora de inicio**.
   - **Fecha y hora de fin**.
   - **Ubicación** (si aplica).
5. Haga clic en el botón **Guardar**.

**Opción B: Desde una actividad**

Al registrar una actividad, active la opción **Crear evento en agenda**. El evento se creará automáticamente con los datos de la actividad y podrá ser visto en su agenda.

**Opción C: Desde el panel principal (agenda general)**

Desde el **Dashboard** puede ver su calendario general y crear eventos rápidos que no estén vinculados necesariamente a un caso.

> **Importante:** Las fechas y horas de los eventos deben ser coherentes: la fecha y hora de fin debe ser posterior a la de inicio. El sistema le avisará si comete este error.

### 2.8 Pausar, reanudar o cerrar el caso

El estado del caso refleja en qué momento del proceso se encuentra y controla qué acciones están disponibles.

| Estado | Qué significa | Acciones disponibles |
|---|---|---|
| **Abierto** | El caso recién fue creado. | **Iniciar Caso** |
| **En proceso** | El caso está activo y se está trabajando. | **Pausar Caso**, **Cerrar Caso**, registrar actividades, documentos, gastos y agenda |
| **En espera** | El caso está detenido temporalmente. | **Reanudar Caso**, **Finalizar Caso** |
| **Culminado** | El caso se cerró. No admite modificaciones. | Solo consulta |

**Pasos para pausar un caso:**

1. Abra el caso en estado **En proceso**.
2. Haga clic en el botón **Pausar Caso**.
3. Confirme la acción. El caso pasará a **En espera**.

**Pasos para reanudar un caso:**

1. Abra el caso en estado **En espera**.
2. Haga clic en el botón **Reanudar Caso**.
3. Confirme la acción. El caso volverá a **En proceso**.

**Pasos para cerrar un caso:**

1. Abra el caso en estado **En proceso** (o **En espera**).
2. Haga clic en el botón **Cerrar Caso** (o **Finalizar Caso**).
3. Confirme la acción. El caso pasará a **Culminado**.

> **Importante: Checklist de verificación previa al cierre.** Antes de hacer clic en **Cerrar Caso**, asegúrese de que todo lo siguiente esté completo. Una vez culminado, el caso ya no admitirá modificaciones.
>
> - [ ] Todas las **actividades** del caso están registradas con su fecha y descripción.
> - [ ] Todos los **documentos** fueron subidos al expediente.
> - [ ] Todos los **gastos** están registrados con su monto y fecha.
> - [ ] Los **eventos de agenda** vinculados al caso están programados o fueron gestionados.
> - [ ] El cliente realizó los **pagos** comprometidos o se coordinó el saldo pendiente.
> - [ ] El **Juzgado**, el **Número de expediente** y el **Título** del caso están correctamente actualizados.

> **Importante:** Una vez que el caso está **Culminado**, ya no se pueden registrar actividades, documentos, gastos ni eventos de agenda. Asegúrese de registrar toda la información pendiente antes de cerrar el caso.

> **Consejo:** Si el caso está detenido por motivos ajenos al estudio (por ejemplo, esperando una resolución del juzgado), use **Pausar Caso** en lugar de dejarlo abierto. Así el reporte de actividad reflejará correctamente los casos realmente activos.

---

## Capítulo 3: Administrador / Dueño del Estudio

Como administrador, usted tiene el control total del sistema. Puede ver el panel general, consultar todos los reportes y administrar los usuarios, roles, permisos y las configuraciones del estudio.

### 3.1 Panel principal (Dashboard)

El **Dashboard** es la primera pantalla que ve al ingresar al sistema. Le ofrece una vista general de:

- **Consultas**: cantidad de consultas registradas y su estado.
- **Casos**: cantidad de casos y su etapa (abiertos, en proceso, en espera, culminados).
- **Actividad del equipo**: indicadores de la carga de trabajo.
- **Agenda**: sus próximos eventos del día y del mes.

Desde el **Dashboard** también puede acceder rápidamente a los módulos principales del menú lateral.

### 3.2 Reportes

El módulo de **Reportes** es la herramienta más poderosa del sistema para usted. Le permite medir la productividad del estudio, sus ingresos, sus egresos y la calidad del servicio.

En el menú lateral, dentro de la sección **Reportes**, encontrará las siguientes opciones:

**1. Financiero Integral**

- **Financiero Integral**: resumen completo de la situación financiera del estudio.
- **Caja**: detalle de los movimientos de efectivo (ingresos por pagos y egresos por gastos).
- **Rentabilidad**: permite comparar los ingresos de los casos contra sus gastos para determinar cuánto se gana (o se pierde) en cada uno.

**2. Clientes y Cobranza**

- **Clientes y Cobranza**: información general de los clientes y el estado de sus pagos.
- **Detalle de Cobranza**: permite ver cuota por cuota qué clientes están al día, cuáles tienen pagos parciales y cuáles están vencidos. Es la herramienta ideal para decidir a qué clientes llamar para cobrar.

**3. Actividad y Recursos**

- **Actividad y Recursos**: mide cuánto trabajo se está realizando en el estudio (actividades registradas, casos activos, etc.).
- **Abogados**: permite comparar la productividad de cada abogado: cuántas actividades registra, cuántos casos atiende, cuánto genera en ingresos y cuánto gasta.
- **Agenda**: muestra los eventos registrados para conocer la ocupación de la agenda del equipo.

**4. Conversión Comercial**

- **Conversión Comercial**: muestra cuántas consultas se convierten en casos y cuántas se pierden. Con este reporte usted puede evaluar si el seguimiento de prospectos está funcionando.

> **Consejo:** Para tomar decisiones, combine la información de varios reportes. Por ejemplo: el reporte de **Abogados** le muestra el ingreso generado por cada profesional, y el reporte de **Rentabilidad** le muestra cuánto de ese ingreso se convirtió realmente en ganancia después de descontar los gastos.

> **Importante:** La calidad de los reportes depende de la información registrada por todo el equipo. Si los abogados no registran sus actividades y gastos, o si recepción no registra los pagos y seguimientos, los reportes mostrarán datos incompletos.

### 3.3 Administración de usuarios, roles y permisos

En el menú **Administración** usted puede gestionar quién accede al sistema y con qué nivel de acceso.

**Usuarios**

1. Ingrese a **Administración → Usuarios**.
2. Haga clic en el botón **Nuevo usuario**.
3. Complete los datos personales, el correo y el **número de celular** del usuario.
4. Asigne un **rol** (por ejemplo: Administrador, Recepcionista, Abogado).
5. Guarde los cambios.

> **Importante:** Registre siempre el número de celular correcto de cada usuario. El sistema lo usará para las notificaciones automáticas dirigidas a los abogados (por ejemplo, cuando se les asigna una consulta).

**Roles y Permisos**

Los módulos **Roles** y **Permisos** le permiten definir qué puede hacer cada perfil dentro del sistema. Por ejemplo, puede decidir si un rol puede editar consultas, generar casos o solo ver la información.

> **Consejo:** No otorgue permisos de administración a usuarios que no los necesiten. Entre menos permisos tenga un usuario, menor es el riesgo de que modifique información que no le corresponde.

### 3.4 Auditoría del sistema

El módulo **Auditoría** registra un historial de las acciones importantes realizadas en el sistema (quién creó, modificó o eliminó información y cuándo). Usted puede consultar este historial siempre que necesite verificar cómo se realizó un cambio.

> **Importante:** Si detecta información incorrecta en el sistema, revise el módulo de **Auditoría** para identificar cuándo y por quién se registró el dato. Esto le permite tomar acciones correctivas con información confiable.

---

## Gestión de Notificaciones

Las notificaciones automáticas son uno de los pilares del sistema, porque permiten que la información fluya sin necesidad de que alguien tenga que avisar manualmente a cada persona. Para que estas notificaciones funcionen correctamente, **los números telefónicos deben estar bien registrados**, tanto los de los abogados como los de los clientes.

### ¿Por qué es tan importante el número de celular?

- **Notificaciones a los abogados**: cuando recepción crea una consulta, el sistema debe poder avisarle al abogado asignado que tiene una nueva consulta por atender. Si el número de celular del abogado no está bien registrado, la notificación no llegará y el abogado podría tardar en enterarse.
- **Notificaciones a los clientes**: el sistema puede enviar recordatorios a los clientes sobre cuotas por vencer, citas o audiencias. Si el número de celular del cliente está mal escrito o desactualizado, el cliente nunca recibirá el aviso y el estudio podrá tener problemas de cobranza o faltas a citas importantes.
- **Confirmación de la comunicación**: en ambos casos, contar con el número correcto garantiza que la comunicación entre el estudio, sus abogados y sus clientes sea efectiva y oportuna.

### ¿Dónde se configuran las notificaciones?

1. Ingrese a **Administración → Notificaciones**.
2. Desde allí podrá crear y administrar las notificaciones automáticas del sistema.
3. Revise periódicamente que los **números de celular de los usuarios** (abogados y personal) estén actualizados en el módulo **Administración → Usuarios**.
4. Revise que los **números de celular de los clientes** estén actualizados en el módulo **Clientes**.

### Recomendaciones para que las notificaciones funcionen correctamente

- **Verifique el número al momento de registrar**: tanto al crear un cliente como al crear un usuario, confirme el número de celular directamente con la persona.
- **Use el formato correcto**: registre el número con su código de país si corresponde (por ejemplo, +51 999 999 999 para Perú). Esto evita problemas si el sistema envía mensajes internacionales.
- **Actualice los datos cuando cambien**: si un cliente o un abogado cambia de número de celular, actualice la información de inmediato en el sistema.
- **No dependa solo del teléfono fijo**: los teléfonos fijos no reciben mensajes de texto ni notificaciones por WhatsApp. Asegúrese de registrar siempre un número de **celular**.
- **Haga una prueba**: después de configurar una notificación, realice una prueba para confirmar que el mensaje llega correctamente al destinatario.

> **Importante:** Una notificación mal configurada o con un número incorrecto puede hacer que un cliente no se entere del vencimiento de una cuota o que un abogado no sepa que tiene una consulta nueva. Dedique unos minutos a verificar que todos los números estén correctos.

> **Consejo:** Designe a una persona responsable (idealmente recepción) para que actualice los números de celular de los clientes cada vez que haya un cambio. Así la información siempre estará al día y las notificaciones llegarán a quien corresponde.

---

## Solución de Problemas Comunes (FAQ)

A continuación, encontrará las respuestas a las situaciones más frecuentes que pueden presentarse al usar el sistema.

### 1. ¿Por qué no aparece el botón "Iniciar Caso"?

El botón **Iniciar Caso** solo aparece cuando el caso se encuentra en estado **Abierto**, es decir, recién creado. Si el caso ya está en **En proceso**, **En espera** o **Culminado**, el botón desaparece y en su lugar aparecen las opciones correspondientes a cada estado (**Pausar Caso**, **Reanudar Caso**, **Cerrar Caso**, etc.).

**Qué hacer:**

1. Verifique el estado actual del caso en la cabecera del expediente.
2. Si el caso está **Abierto**, haga clic en **Iniciar Caso** para habilitar el registro de actividades, documentos, gastos y agenda.
3. Si el caso está en otro estado, utilice el botón que corresponda según lo que necesite hacer.

### 2. ¿Cómo corrijo un pago mal registrado?

Si se registró un pago por error (monto incorrecto, método equivocado o pago aplicado a la cuota que no correspondía), la corrección debe ser realizada con cuidado porque los pagos están vinculados al resumen financiero de la consulta y a los reportes.

**Qué hacer:**

1. Abra la consulta del cliente y revise la cuota afectada en la tabla de **Cuotas**.
2. Verifique si el error es solo de monto o de aplicación.
3. Por seguridad, los pagos no se eliminan directamente por el personal de recepción. Comuníquese con el **administrador del sistema**, quien podrá revisar el módulo de **Auditoría** para identificar el registro exacto y corregirlo o eliminarlo según corresponda.
4. Una vez corregido, confirme que el resumen financiero (Total, Pagado, Pendiente) vuelva a ser el correcto.

> **Importante:** Nunca registre un "pago de más" o un "pago de menos" para compensar otro error. Eso desvirtúa los reportes financieros. Siempre solicite la corrección adecuada del registro original.

### 3. ¿Qué hago si una consulta se quedó en estado "Prospecto" por error?

Si el cliente ya tomó una decisión pero la consulta sigue mostrando el estado **Prospecto**, el personal de recepción debe registrar un nuevo seguimiento con el resultado correcto.

**Qué hacer:**

1. Abra la consulta del cliente.
2. En la sección **Seguimientos**, haga clic en **Nuevo seguimiento**.
3. Seleccione el **Resultado** que corresponda a la decisión real del cliente:
   - **Acepta contratar**: el sistema ofrecerá la opción **Generar caso al guardar** para crear el caso automáticamente.
   - **Rechazado**: el sistema ofrecerá la opción **Rechazar consulta al guardar** para marcar la consulta como rechazada.
4. Guarde el seguimiento. La consulta y el caso (si aplica) se actualizarán automáticamente.

### 4. ¿Por qué no puedo registrar actividades, documentos ni gastos en un caso?

El registro de actividades, documentos, gastos y agenda solo está habilitado cuando el caso está en estado **En proceso**.

**Qué hacer:**

1. Abra el caso y verifique su estado en la cabecera.
2. Si el caso está **Abierto**, primero debe hacer clic en **Iniciar Caso**. Al confirmar, las pestañas quedarán habilitadas.
3. Si el caso está **En espera**, deberá hacer clic en **Reanudar Caso** para volver a dejarlo **En proceso**.
4. Si el caso está **Culminado**, ya no admite modificaciones. En ese caso, no es posible registrar información nueva; se trataría de un caso cerrado que solo puede consultarse.

### 5. ¿Qué hago si un cliente no recibe las notificaciones?

Las notificaciones dependen principalmente de que el número de celular del cliente esté correctamente registrado y de que la configuración de notificaciones esté activa.

**Qué hacer:**

1. Ingrese al módulo **Clientes** y abra la ficha del cliente.
2. Verifique que el **Teléfono / Celular** esté bien escrito, incluya el código de país si corresponde (por ejemplo, +51 999 999 999 para Perú) y que sea un número de celular (los teléfonos fijos no reciben mensajes de texto ni avisos por WhatsApp).
3. Si el número estaba incorrecto, corríjalo y guarde los cambios.
4. Verifique en **Administración → Notificaciones** que la notificación correspondiente esté creada y activa.
5. Realice una prueba de envío para confirmar que la notificación llega correctamente.

> **Consejo:** Si después de verificar el número y la configuración el cliente sigue sin recibir notificaciones, revise con el administrador si el proveedor de mensajería del sistema está operando correctamente.

---

## Glosario rápido

| Término | Significado |
|---|---|
| **Consulta** | El primer registro cuando un cliente solicita información o asesoría al estudio. Puede estar en estado Nuevo, Prospecto, Aceptado o Rechazado. |
| **Prospecto** | Estado de la consulta cuando el cliente aún no decide si contratará los servicios. Recepción debe realizarle seguimiento. |
| **Caso** | El expediente que se genera cuando el cliente acepta contratar los servicios. Hereda los datos de la consulta. |
| **Cuota** | Cada uno de los pagos parciales en los que se divide el monto total acordado con el cliente. |
| **Actividad** | Registro del trabajo realizado en un caso (actos procesales, avance judicial, comunicación o nota). |
| **Documento** | Archivo adjunto al caso (escritos, resoluciones, contratos, evidencias, etc.). |
| **Gasto** | Desembolso realizado por el estudio por cuenta de un caso (tasas, movilidad, copias, notaría, etc.). |
| **Agenda** | Calendario de eventos (audiencias, reuniones, vencimientos, llamadas, tareas). |
| **Seguimiento** | Registro de las comunicaciones con un prospecto para conocer su decisión. |
| **Reporte** | Informe que permite al administrador medir finanzas, cobranza, productividad y conversión comercial del estudio. |
| **Notificación** | Aviso automático que el sistema envía a los abogados o clientes (por ejemplo, al asignar una consulta o al acercarse un vencimiento). |
| **Auditoría** | Historial de acciones realizadas en el sistema que permite saber quién hizo cada cambio y cuándo. |

---

*Fin del manual. Si tiene dudas sobre alguna función específica, consulte con el administrador del sistema para recibir asistencia adicional.*