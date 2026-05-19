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

        return $response->json();
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
        | Perú
        |--------------------------------------------------------------------------
        */

        if (strlen($number) === 9) {

            return '51' . $number;

        }

        if (
            strlen($number) === 11
            &&
            str_starts_with($number, '51')
        ) {

            return $number;

        }

        throw new Exception(
            "Número inválido: {$number}"
        );
    }
}