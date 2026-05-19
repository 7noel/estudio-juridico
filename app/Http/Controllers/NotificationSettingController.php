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

        $request->validate([

            'label' => 'required',

            'value' => 'nullable',

            'type' => 'required',

        ]);

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