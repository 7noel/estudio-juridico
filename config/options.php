<?php

return [

    /*
    |--------------------------------------------------------------------------
    | DOCUMENTOS CLIENTES
    |--------------------------------------------------------------------------
    */

    'client_document_types' => [

        '1' => 'DNI',
        '4' => 'CEX',
        '7' => 'PAS',
        '6' => 'RUC',
        'A' => 'CÉDULA DIPLOMÁTICA',

    ],

    /*
    |--------------------------------------------------------------------------
    | DOCUMENTOS EMPLEADOS
    |--------------------------------------------------------------------------
    */

    'employee_document_types' => [

        '1' => 'DNI',
        '4' => 'CEX',
        '7' => 'PAS',

    ],

    /*
    |--------------------------------------------------------------------------
    | TIPOS DE SERVICIO
    |--------------------------------------------------------------------------
    */

    'service_types' => [

        'process' => 'Proceso Judicial',

        'procedural_act' => 'Acto Procesal',

        'extrajudicial_act' => 'Acto Extrajudicial',

    ],

    /*
    |--------------------------------------------------------------------------
    | ESTADOS CONSULTA
    |--------------------------------------------------------------------------
    */

    'consultation_statuses' => [
        'new'        => 'Nueva',
        'assigned'   => 'Asignada',
        'evaluating' => 'En evaluación',
        'quoted'     => 'Cotizado',
        'accepted'   => 'Aceptado',
        'rejected'   => 'Rechazado',
    ],

    'default_consultation_status' => 'new',

    'consultation_status_colors' => [
        'new'        => 'secondary',
        'assigned'   => 'primary',
        'evaluating' => 'info',
        'quoted'     => 'warning',
        'accepted'   => 'success',
        'rejected'   => 'danger',
    ],
    
    /*
    |--------------------------------------------------------------------------
    | ESTADOS CASO
    |--------------------------------------------------------------------------
    */

    'case_statuses' => [
        'pending'   => 'Pendiente',
        'active'    => 'Activo',
        'suspended' => 'Suspendido',
        'closed'    => 'Cerrado',
        'archived'  => 'Archivado',
    ],

    'default_case_status' => 'pending',

    /*
    |--------------------------------------------------------------------------
    | MÉTODOS DE PAGO
    |--------------------------------------------------------------------------
    */

    'payment_methods' => [

        'cash' => 'Efectivo',

        'transfer' => 'Transferencia',

        'deposit' => 'Depósito',

        'yape' => 'Yape',

        'plin' => 'Plin',

        'card' => 'Tarjeta',

    ],

    /*
    |--------------------------------------------------------------------------
    | TIPOS DE COMUNICACIÓN
    |--------------------------------------------------------------------------
    */

    'communication_types' => [

        'phone' => 'Llamada Telefónica',

        'whatsapp' => 'WhatsApp',

        'email' => 'Correo Electrónico',

        'meeting' => 'Reunión',

        'sms' => 'SMS',

    ],

    /*
    |--------------------------------------------------------------------------
    | TIPOS DE ACTIVIDADES LEGALES
    |--------------------------------------------------------------------------
    */

    'activity_types' => [

        'hearing' => 'Audiencia',

        'filing' => 'Presentación de Escrito',

        'resolution' => 'Recepción de Resolución',

        'diligence' => 'Diligencia',

        'meeting' => 'Reunión con Cliente',

        'call' => 'Llamada',

        'other' => 'Otro',

    ],

    /*
    |--------------------------------------------------------------------------
    | TIPOS DE DOCUMENTOS
    |--------------------------------------------------------------------------
    */

    'document_types' => [

        'strategy' => 'Estrategia Legal',

        'contract' => 'Contrato',

        'resolution' => 'Resolución Judicial',

        'evidence' => 'Evidencia',

        'writing' => 'Escrito Judicial',

        'dni' => 'Documento de Identidad',

        'other' => 'Otro',

    ],

];