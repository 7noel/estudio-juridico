<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\NotificationLog;
use App\Models\NotificationSetting;
use App\Models\ConsultationInstallment;
use App\Services\WhatsAppService;
use App\Jobs\SendWhatsAppMessageJob;

class SendQuotaRemindersCommand extends Command
{
    protected $signature = 'reminders:quotas';

    protected $description = 'Enviar recordatorios de cuotas';

    public function handle()
    {
        /*
        |--------------------------------------------------------------------------
        | Validar si está habilitado
        |--------------------------------------------------------------------------
        */

        $enabled = NotificationSetting::get(
            'quota_reminder_enabled'
        );

        if (!$enabled) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Configuración
        |--------------------------------------------------------------------------
        */

        $daysBefore = (int) NotificationSetting::get(
            'quota_reminder_days_before',
            1
        );

        $hour = NotificationSetting::get(
            'quota_reminder_hour',
            '09:00'
        );

        /*
        |--------------------------------------------------------------------------
        | Ejecutar solo a la hora configurada
        |--------------------------------------------------------------------------
        */

        $scheduledTime = today()->setTimeFromTimeString($hour);

        $now = now();

        $diffInMinutes = $scheduledTime->diffInMinutes(
            $now,
            false
        );

        if ($diffInMinutes < 0 || $diffInMinutes > 10) {

            return;

        }

        /*
        |--------------------------------------------------------------------------
        | Fecha objetivo
        |--------------------------------------------------------------------------
        */

        $targetDate = now()
            ->addDays($daysBefore)
            ->format('Y-m-d');

        /*
        |--------------------------------------------------------------------------
        | Buscar cuotas pendientes
        |--------------------------------------------------------------------------
        */

        $installments = ConsultationInstallment::query()

            ->whereDate('due_date', $targetDate)

            /*
            |--------------------------------------------------------------------------
            | Cuotas NO pagadas
            |--------------------------------------------------------------------------
            */

            ->whereDoesntHave('payments')

            /*
            |--------------------------------------------------------------------------
            | Relaciones
            |--------------------------------------------------------------------------
            */

            ->with([
                'consultation.client'
            ])

            ->get();

        $whatsapp = app(WhatsAppService::class);

        foreach ($installments as $installment) {

            $client = $installment
                ->consultation
                ?->client;

            /*
            |--------------------------------------------------------------------------
            | Validar cliente
            |--------------------------------------------------------------------------
            */

            if (!$client) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Validar celular
            |--------------------------------------------------------------------------
            */

            if (!$client->mobile) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Evitar duplicados
            |--------------------------------------------------------------------------
            */

            $alreadySent = NotificationLog::query()

                ->where('type', 'quota_reminder')

                ->where('related_id', $installment->id)

                ->exists();

            if ($alreadySent) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Mensaje
            |--------------------------------------------------------------------------
            */

            $message =
                "Hola {$client->name},\n\n"
                ."Le recordamos que su cuota correspondiente "
                ."a su consulta jurídica vence el día "
                .date('d/m/Y', strtotime($installment->due_date))
                .".\n\n"
                ."Si ya realizó el pago, puede omitir este mensaje.\n\n"
                ."Gracias.";

            /*
            |--------------------------------------------------------------------------
            | Enviar WhatsApp
            |--------------------------------------------------------------------------
            */

            SendWhatsAppMessageJob::dispatch(

                $client->mobile,

                $message,

                'quota_reminder',

                $installment->id

            );
            /*
            |--------------------------------------------------------------------------
            | Registrar envío
            |--------------------------------------------------------------------------
            */

            NotificationLog::create([

                'type' => 'quota_reminder',

                'related_id' => $installment->id,

                'phone' => $client->mobile,

                'sent_at' => now(),

            ]);
            
        }
    }
}