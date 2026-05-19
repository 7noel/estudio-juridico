<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NotificationSetting;

class NotificationSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [

            [
                'key' => 'quota_reminder_enabled',
                'label' => 'Activar recordatorio de cuotas',
                'value' => '1',
                'type' => 'boolean',
            ],

            [
                'key' => 'quota_reminder_days_before',
                'label' => 'Días antes para recordar cuotas',
                'value' => '1',
                'type' => 'integer',
            ],

            [
                'key' => 'quota_reminder_hour',
                'label' => 'Hora recordatorio cuotas',
                'value' => '09:00',
                'type' => 'time',
            ],

            [
                'key' => 'agenda_reminder_enabled',
                'label' => 'Activar recordatorio agenda',
                'value' => '1',
                'type' => 'boolean',
            ],

            [
                'key' => 'agenda_reminder_minutes_before',
                'label' => 'Minutos antes agenda',
                'value' => '60',
                'type' => 'integer',
            ],

            [
                'key' => 'inactive_client_enabled',
                'label' => 'Activar alerta inactividad',
                'value' => '1',
                'type' => 'boolean',
            ],

            [
                'key' => 'inactive_client_days',
                'label' => 'Días sin contacto',
                'value' => '15',
                'type' => 'integer',
            ],

            [
                'key' => 'inactive_client_hour',
                'label' => 'Hora alerta inactividad',
                'value' => '09:00',
                'type' => 'time',
            ],

        ];

        foreach ($settings as $setting) {

            NotificationSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );

        }
    }
}