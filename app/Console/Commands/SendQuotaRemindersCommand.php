<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\NotificationLog;
use App\Models\NotificationSetting;
use App\Models\ConsultationInstallment;
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
        | Ejecutar solo dentro de la ventana de la hora configurada
        | (hora configurada + 15 minutos de tolerancia)
        |--------------------------------------------------------------------------
        */

        $windowStart = today()->setTimeFromTimeString($hour);

        $windowEnd = today()
            ->setTimeFromTimeString($hour)
            ->addMinutes(15);

        if (! now()->between($windowStart, $windowEnd)) {

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
            | Nunca recordar cuotas ya vencidas
            |--------------------------------------------------------------------------
            */

            ->whereDate('due_date', '>=', today()->toDateString())

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

                ->where('status', '!=', 'failed')

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
            | Registrar intento de envío (pendiente)
            |--------------------------------------------------------------------------
            */

            NotificationLog::create([

                'type' => 'quota_reminder',

                'related_id' => $installment->id,

                'phone' => $client->mobile,

                'sent_at' => now(),

                'status' => 'pending',

            ]);

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
            
        }
    }
}