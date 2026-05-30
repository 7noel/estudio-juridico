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
        'open'        => 'Abierto',
        'in_progress' => 'En proceso',
        'on_hold'     => 'En espera',
        'closed'      => 'Cerrado',
    ],

    'default_case_status' => 'open',

    'case_status_colors' => [
        'open'        => 'primary',
        'in_progress' => 'warning',
        'on_hold'     => 'secondary',
        'closed'      => 'success',
    ],

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

    'activity_main_types' => [
        'legal' => 'Actividad legal',
        'communication' => 'Comunicación',
        'note' => 'Nota',
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

    'agenda_event_types' => [

        'hearing' => 'Audiencia',

        'deadline' => 'Vencimiento',

        'meeting' => 'Reunión',

        'task' => 'Tarea',

        'call' => 'Llamada',

        'other' => 'Otro',

    ],

    'agenda_event_colors' => [

        'hearing' => [
            'background' => '#dc3545',
            'text' => '#ffffff',
        ],

        'deadline' => [
            'background' => '#ffc107',
            'text' => '#000000',
        ],

        'meeting' => [
            'background' => '#0d6efd',
            'text' => '#ffffff',
        ],

        'task' => [
            'background' => '#198754',
            'text' => '#ffffff',
        ],

        'call' => [
            'background' => '#0dcaf0',
            'text' => '#000000',
        ],

        'other' => [
            'background' => '#6c757d',
            'text' => '#ffffff',
        ],

    ],

    'expense_categories' => [

        'judicial_fee' => 'Tasa judicial',

        'transport' => 'Movilidad',

        'copies' => 'Copias e impresiones',

        'sunarp' => 'SUNARP',

        'notary' => 'Notaría',

        'expertise' => 'Peritaje',

        'travel' => 'Viáticos',

        'office' => 'Gastos de oficina',

        'services' => 'Servicios',

        'marketing' => 'Marketing',

        'taxes' => 'Impuestos',

        'other' => 'Otros',

    ],

];