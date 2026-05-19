<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\NotificationLog;
use App\Models\NotificationSetting;
use App\Jobs\SendWhatsAppMessageJob;

class SendAgendaRemindersCommand extends Command
{
    protected $signature =
        'reminders:agenda';

    protected $description =
        'Enviar recordatorios agenda';

    public function handle()
    {
        /*
        |--------------------------------------------------------------------------
        | Configuración
        |--------------------------------------------------------------------------
        */

        $enabled = NotificationSetting::get(
            'agenda_reminder_enabled'
        );

        if (!$enabled) {
            return;
        }

        $minutesBefore = (int) NotificationSetting::get(
            'agenda_reminder_minutes_before',
            60
        );

        /*
        |--------------------------------------------------------------------------
        | Ventana tiempo
        |--------------------------------------------------------------------------
        */

        $startWindow = now()
            ->addMinutes($minutesBefore)
            ->startOfMinute()
            ->format('Y-m-d H:i:s');

        $endWindow = now()
            ->addMinutes($minutesBefore + 10)
            ->endOfMinute()
            ->format('Y-m-d H:i:s');
        //dd($startWindow);

        /*
        |--------------------------------------------------------------------------
        | QUERY 1
        | Eventos CON caso
        |--------------------------------------------------------------------------
        */

        $eventsWithCase = DB::table('agenda_events')

            ->join(
                'cases',
                'cases.id',
                '=',
                'agenda_events.case_id'
            )

            ->join(
                'users',
                'users.id',
                '=',
                'cases.lawyer_id'
            )

            ->where(
                'cases.status',
                'in_progress'
            )

            ->whereBetween(

                'agenda_events.start_datetime',

                [
                    $startWindow,
                    $endWindow
                ]

            )

            ->select([

                'agenda_events.id',

                'agenda_events.title',

                'agenda_events.description',

                'agenda_events.start_datetime',

                'agenda_events.location',

                'users.name as user_name',

                'users.mobile as user_mobile',

                'cases.title as case_title',

                'cases.id as case_id',

            ])

            ->get();

            //dd($eventsWithCase);
        /*
        |--------------------------------------------------------------------------
        | QUERY 2
        | Eventos SIN caso
        |--------------------------------------------------------------------------
        */

        $eventsWithoutCase = DB::table('agenda_events')

            ->join(
                'users',
                'users.id',
                '=',
                'agenda_events.created_by'
            )

            ->whereNull(
                'agenda_events.case_id'
            )

            ->whereBetween(

                'agenda_events.start_datetime',

                [
                    $startWindow,
                    $endWindow
                ]

            )

            ->select([

                'agenda_events.id',

                'agenda_events.title',

                'agenda_events.description',

                'agenda_events.start_datetime',

                'agenda_events.location',

                'users.name as user_name',

                'users.mobile as user_mobile',

            ])

            ->get();

        /*
        |--------------------------------------------------------------------------
        | Procesar eventos CON caso
        |--------------------------------------------------------------------------
        */

        foreach ($eventsWithCase as $event) {

            if (!$event->user_mobile) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Evitar duplicados
            |--------------------------------------------------------------------------
            */

            $alreadySent = NotificationLog::query()

                ->where(
                    'type',
                    'agenda_event'
                )

                ->where(
                    'related_id',
                    $event->id
                )

                ->exists();

            if ($alreadySent) {
                continue;
            }

            $formattedDate = Carbon::parse(
                $event->start_datetime
            )->format('d/m/Y H:i');

            // $url = route(
            //     'agenda-events.show',
            //     $event->id
            // );

            $message =
                "Estimado(a) {$event->user_name},\n\n"

                ."Le recordamos el siguiente evento "
                ."agendado:\n\n"

                ."Evento: {$event->title}\n"

                ."Caso: {$event->case_title}\n"

                ."Fecha y hora: {$formattedDate}\n";

            if ($event->location) {

                $message .=
                    "Ubicación: {$event->location}\n";

            }

            $message .=
                "\n"

                ."Descripción:\n"

                .($event->description ?? '-');

                // ."\n\n"

                // ."Acceder al evento:\n"

                // ."{$url}";

            SendWhatsAppMessageJob::dispatch(

                $event->user_mobile,

                $message

            );

            /*
            |--------------------------------------------------------------------------
            | Registrar log
            |--------------------------------------------------------------------------
            */

            NotificationLog::create([

                'type' =>
                    'agenda_event',

                'related_id' =>
                    $event->id,

                'phone' =>
                    $event->user_mobile,

                'sent_at' =>
                    now(),

            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Procesar eventos SIN caso
        |--------------------------------------------------------------------------
        */

        foreach ($eventsWithoutCase as $event) {

            if (!$event->user_mobile) {
                continue;
            }

            $alreadySent = NotificationLog::query()

                ->where(
                    'type',
                    'agenda_event'
                )

                ->where(
                    'related_id',
                    $event->id
                )

                ->exists();

            if ($alreadySent) {
                continue;
            }

            $formattedDate = Carbon::parse(
                $event->start_datetime
            )->format('d/m/Y H:i');

            $url = route(
                'agenda-events.show',
                $event->id
            );

            $message =
                "Estimado(a) {$event->user_name},\n\n"

                ."Le recordamos el siguiente evento "
                ."agendado:\n\n"

                ."Evento: {$event->title}\n"

                ."Fecha y hora: {$formattedDate}\n";

            if ($event->location) {

                $message .=
                    "Ubicación: {$event->location}\n";

            }

            $message .=
                "\n"

                ."Descripción:\n"

                .($event->description ?? '-')

                ."\n\n"

                ."Acceder al evento:\n"

                ."{$url}";

            SendWhatsAppMessageJob::dispatch(

                $event->user_mobile,

                $message

            );

            /*
            |--------------------------------------------------------------------------
            | Registrar log
            |--------------------------------------------------------------------------
            */

            NotificationLog::create([

                'type' =>
                    'agenda_event',

                'related_id' =>
                    $event->id,

                'phone' =>
                    $event->user_mobile,

                'sent_at' =>
                    now(),

            ]);
        }
    }
}