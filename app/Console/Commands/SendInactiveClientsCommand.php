<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\NotificationLog;
use App\Models\NotificationSetting;
use App\Jobs\SendWhatsAppMessageJob;

class SendInactiveClientsCommand extends Command
{
    protected $signature =
        'reminders:inactive-clients';

    protected $description =
        'Recordatorio de clientes inactivos';

    public function handle()
    {
        /*
        |--------------------------------------------------------------------------
        | Configuración
        |--------------------------------------------------------------------------
        */

        $enabled = NotificationSetting::get(
            'inactive_client_enabled'
        );

        if (!$enabled) {
            return;
        }

        $days = (int) NotificationSetting::get(
            'inactive_client_days',
            15
        );

        $hour = NotificationSetting::get(
            'inactive_client_hour',
            '09:00'
        );

        /*
        |--------------------------------------------------------------------------
        | Tolerancia horaria
        |--------------------------------------------------------------------------
        */

        $scheduledTime = today()
            ->setTimeFromTimeString($hour);

        $diffInMinutes = $scheduledTime
            ->diffInMinutes(now(), false);

        if (
            $diffInMinutes < 0
            ||
            $diffInMinutes > 100
        ) {

            return;

        }

        /*
        |--------------------------------------------------------------------------
        | Fecha límite
        |--------------------------------------------------------------------------
        */

        $limitDate = now()
            ->subDays($days);

        /*
        |--------------------------------------------------------------------------
        | QUERY 1
        | Casos SIN communication
        |--------------------------------------------------------------------------
        */

        $casesWithoutCommunication = DB::table('cases')

            ->leftJoin(
                'clients',
                'clients.id',
                '=',
                'cases.client_id'
            )

            ->leftJoin(
                'users',
                'users.id',
                '=',
                'cases.lawyer_id'
            )

            ->where(
                'cases.status',
                'in_progress'
            )

            ->where(
                'cases.opened_at',
                '<=',
                $limitDate
            )

            ->whereNotExists(function ($query) {

                $query->select(DB::raw(1))

                    ->from('case_activities')

                    ->whereColumn(
                        'case_activities.case_id',
                        'cases.id'
                    )

                    ->where(
                        'case_activities.type',
                        'communication'
                    );

            })

            ->select([

                'cases.id',

                'cases.title',

                'cases.opened_at',

                'clients.full_name as client_name',

                'users.name as lawyer_name',

                'users.mobile as lawyer_mobile',

            ])

            ->get();
        /*
        |--------------------------------------------------------------------------
        | Procesar casos SIN communication
        |--------------------------------------------------------------------------
        */

        foreach ($casesWithoutCommunication as $case) {

            if (!$case->lawyer_mobile) {
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
                    'case_without_communication'
                )

                ->where(
                    'related_id',
                    $case->id
                )

                ->exists();

            if ($alreadySent) {
                continue;
            }

            $daysWithoutCommunication =
                now()->diffInDays(
                    $case->opened_at
                );

            $url = route(
                'cases.show',
                $case->id
            );

            $message =
                "Estimado(a) {$case->lawyer_name},\n\n"

                ."El caso '{$case->title}' "
                ."no registra ninguna comunicación "
                ."con el cliente "
                ."{$case->client_name}.\n\n"

                ."Han transcurrido "
                ."{$daysWithoutCommunication} días "
                ."desde la apertura del caso.\n\n"

                ."Se recomienda realizar seguimiento "
                ."y registrar la comunicación correspondiente.\n\n"

                ."Acceder al caso:\n"

                ."{$url}";

            SendWhatsAppMessageJob::dispatch(

                $case->lawyer_mobile,

                $message

            );

            /*
            |--------------------------------------------------------------------------
            | Registrar log
            |--------------------------------------------------------------------------
            */

            NotificationLog::create([

                'type' =>
                    'case_without_communication',

                'related_id' =>
                    $case->id,

                'phone' =>
                    $case->lawyer_mobile,

                'sent_at' =>
                    now(),

            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | QUERY 2
        | Cliente inactivo
        |--------------------------------------------------------------------------
        */

        $inactiveClients = DB::table('cases')

            ->join(
                'clients',
                'clients.id',
                '=',
                'cases.client_id'
            )

            ->join(
                'users',
                'users.id',
                '=',
                'cases.lawyer_id'
            )

            ->joinSub(

                DB::table('case_activities as ca1')

                    ->select([

                        'ca1.case_id',

                        DB::raw('MAX(ca1.activity_at) as last_activity_at')

                    ])

                    ->where(
                        'ca1.type',
                        'communication'
                    )

                    ->groupBy(
                        'ca1.case_id'
                    ),

                'latest_activity_dates',

                function ($join) {

                    $join->on(
                        'latest_activity_dates.case_id',
                        '=',
                        'cases.id'
                    );

                }

            )

            ->join(

                'case_activities as latest_activity',

                function ($join) {

                    $join->on(
                        'latest_activity.case_id',
                        '=',
                        'cases.id'
                    )

                    ->on(
                        'latest_activity.activity_at',
                        '=',
                        'latest_activity_dates.last_activity_at'
                    );

                }

            )

            ->where(
                'cases.status',
                'in_progress'
            )

            ->where(
                'latest_activity.activity_at',
                '<=',
                $limitDate
            )

            ->select([

                'cases.id as case_id',

                'cases.title',

                'clients.full_name as client_name',

                'users.name as lawyer_name',

                'users.mobile as lawyer_mobile',

                'latest_activity.id as activity_id',

                'latest_activity.activity_at as last_activity_at',

            ])

            ->get();

        /*
        |--------------------------------------------------------------------------
        | Procesar clientes inactivos
        |--------------------------------------------------------------------------
        */

        foreach ($inactiveClients as $case) {

            if (!$case->lawyer_mobile) {
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
                    'inactive_client'
                )

                ->where(
                    'related_id',
                    $case->activity_id
                )

                ->exists();

            if ($alreadySent) {
                continue;
            }

            $daysWithoutCommunication =
                now()->diffInDays(
                    $case->last_activity_at
                );

            $url = route(
                'cases.show',
                $case->case_id
            );

            $message =
                "Estimado(a) {$case->lawyer_name},\n\n"

                ."Han transcurrido "
                ."{$daysWithoutCommunication} días "
                ."sin registrar comunicación "
                ."con el cliente "
                ."{$case->client_name}.\n\n"

                ."Caso: {$case->title}\n\n"

                ."Se recomienda realizar seguimiento "
                ."y registrar la actividad correspondiente.\n\n"

                ."Acceder al caso:\n"

                ."{$url}";

            SendWhatsAppMessageJob::dispatch(

                $case->lawyer_mobile,

                $message

            );

            /*
            |--------------------------------------------------------------------------
            | Registrar log
            |--------------------------------------------------------------------------
            */

            NotificationLog::create([

                'type' =>
                    'inactive_client',

                'related_id' =>
                    $case->activity_id,

                'phone' =>
                    $case->lawyer_mobile,

                'sent_at' =>
                    now(),

            ]);
        }
    }
}