<?php

namespace App\Http\Controllers;

use App\Models\NotificationSetting;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class NotificationSettingController extends Controller
{
    public function index()
    {
        return view('notification-settings.index');
    }

    public function datatable()
    {
        $query = NotificationSetting::query();

        return DataTables::of($query)

            ->addColumn('actions', function ($row) {

                return '
                    <a href="' . route('notification-settings.edit', $row) . '" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                ';

            })

            ->rawColumns(['actions'])

            ->make(true);
    }

    public function create()
    {
        return view('notification-settings.create');
    }

    public function store(Request $request)
    {
        $request->validate([

            'key' => 'required|unique:notification_settings',

            'label' => 'required',

            'value' => 'nullable',

            'type' => 'required',

        ]);

        NotificationSetting::create($request->all());

        return redirect()
            ->route('notification-settings.index')
            ->with('success', 'Configuración creada');
    }

    public function edit(NotificationSetting $notificationSetting)
    {
        return view(
            'notification-settings.edit',
            compact('notificationSetting')
        );
    }

    public function update(
        Request $request,
        NotificationSetting $notificationSetting
    ) {

        $rules = [

            'label' => 'required',

            'value' => 'nullable',

            'type' => 'required',

        ];

        /*
        |--------------------------------------------------------------------------
        | Validación específica según la clave
        |--------------------------------------------------------------------------
        */

        $specificRules = [

            'quota_reminder_days_before' => 'required|integer|min:0',

            'inactive_client_days' => 'required|integer|min:0',

            'agenda_reminder_minutes_before' => 'required|integer|min:1',

            'quota_reminder_hour' => 'required|date_format:H:i',

            'inactive_client_hour' => 'required|date_format:H:i',

            'quota_reminder_enabled' => 'required|in:0,1',

            'agenda_reminder_enabled' => 'required|in:0,1',

            'inactive_client_enabled' => 'required|in:0,1',

        ];

        if (isset($specificRules[$notificationSetting->key])) {

            $rules['value'] = $specificRules[$notificationSetting->key];

        }

        $request->validate($rules);

        $notificationSetting->update([

            'label' => $request->label,

            'value' => $request->value,

            'type' => $request->type,

        ]);

        return redirect()
            ->route('notification-settings.index')
            ->with('success', 'Configuración actualizada');
    }

    public function destroy(NotificationSetting $notificationSetting)
    {
        $notificationSetting->delete();

        return response()->json([
            'success' => true
        ]);
    }
}