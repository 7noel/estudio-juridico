<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    /*
    |--------------------------------------------------------------------------
    | Enviar texto
    |--------------------------------------------------------------------------
    */

    public function sendText($number, $message)
    {
        return $this->request(

            endpoint: 'message/sendText',

            number: $number,

            payload: [

                'text' => $message

            ]

        );
    }

    /*
    |--------------------------------------------------------------------------
    | Enviar documento
    |--------------------------------------------------------------------------
    */

    public function sendDocument(
        $number,
        $url,
        $fileName
    ) {

        return $this->request(

            endpoint: 'message/sendMedia',

            number: $number,

            payload: [

                'mediatype' => 'document',

                'media' => $url,

                'fileName' => $fileName,

            ]

        );
    }

    /*
    |--------------------------------------------------------------------------
    | Request base
    |--------------------------------------------------------------------------
    */

    protected function request(
        $endpoint,
        $number,
        array $payload = []
    ) {

        /*
        |--------------------------------------------------------------------------
        | Normalizar número
        |--------------------------------------------------------------------------
        */

        $number = $this->normalizePhone($number);

        /*
        |--------------------------------------------------------------------------
        | Configuración
        |--------------------------------------------------------------------------
        */

        $url = config('services.evolution.url');

        $instance = config('services.evolution.instance');

        /*
        |--------------------------------------------------------------------------
        | Payload final
        |--------------------------------------------------------------------------
        */

        $payload = array_merge([

            'number' => $number,

        ], $payload);

        /*
        |--------------------------------------------------------------------------
        | Request
        |--------------------------------------------------------------------------
        */

        $response = Http::withHeaders([

            'apikey' => config('services.evolution.key'),

        ])->post(

            "{$url}/{$endpoint}/{$instance}",

            $payload

        );

        /*
        |--------------------------------------------------------------------------
        | Lanzar excepción en errores HTTP (4xx/5xx)
        |--------------------------------------------------------------------------
        */

        $response->throw();

        $data = $response->json();

        /*
        |--------------------------------------------------------------------------
        | Validar error en el body (Evolution puede responder 200/201 con error)
        |--------------------------------------------------------------------------
        */

        $status = $data['status'] ?? null;

        if (
            (is_int($status) && $status >= 400)
            || in_array($status, ['ERROR', 'error'], true)
            || isset($data['error'])
        ) {

            throw new Exception(
                'Evolution API respondió con error: '
                . json_encode($data)
            );

        }

        return $data;
    }

    /*
    |--------------------------------------------------------------------------
    | Normalizar teléfono
    |--------------------------------------------------------------------------
    */

    public function normalizePhone($number)
    {
        $number = preg_replace('/\D/', '', $number);

        /*
        |--------------------------------------------------------------------------
        | Prefijo internacional 00 → se elimina (equivale a +)
        |--------------------------------------------------------------------------
        */

        if (str_starts_with($number, '00')) {

            $number = substr($number, 2);

        }

        /*
        |--------------------------------------------------------------------------
        | Perú: 9 dígitos
        |--------------------------------------------------------------------------
        */

        if (strlen($number) === 9) {

            return '51' . $number;

        }

        /*
        |--------------------------------------------------------------------------
        | Internacional: ya incluye código de país (10 a 15 dígitos)
        |--------------------------------------------------------------------------
        */

        if (strlen($number) >= 10 && strlen($number) <= 15) {

            return $number;

        }

        throw new Exception(
            "Número inválido: {$number}"
        );
    }
}