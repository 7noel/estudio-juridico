<?php

namespace App\Jobs;

use Throwable;
use App\Models\NotificationLog;
use Illuminate\Foundation\Queue\Queueable;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendWhatsAppMessageJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $tries = 3;

    public $timeout = 60;

    protected $number;

    protected $message;

    protected $type;

    protected $relatedId;

    public function __construct(
        $number,
        $message,
        $type = null,
        $relatedId = null
    ) {

        $this->number = $number;

        $this->message = $message;

        $this->type = $type;

        $this->relatedId = $relatedId;
    }

    public function handle(): void
    {
        //dd("aqui command");
        $service = app(WhatsAppService::class);

        $response = $service->sendText(
            $this->number,
            $this->message
        );

        /*
        |--------------------------------------------------------------------------
        | Registrar envío
        |--------------------------------------------------------------------------
        */

        if ($this->type && $this->relatedId) {

            NotificationLog::create([

                'type' => $this->type,

                'related_id' => $this->relatedId,

                'phone' => $this->number,

                'sent_at' => now(),

            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Log opcional
        |--------------------------------------------------------------------------
        */

        Log::info('WhatsApp enviado', [

            'number' => $this->number,

            'response' => $response,

        ]);
    }

    public function failed(Throwable $exception): void
    {
        Log::error('Error enviando WhatsApp', [

            'number' => $this->number,

            'message' => $this->message,

            'error' => $exception->getMessage(),

        ]);
    }
}
