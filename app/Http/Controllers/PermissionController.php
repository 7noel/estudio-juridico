<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:view permissions')->only('index');
        $this->middleware('permission:create permissions')->only('create','store');
        $this->middleware('permission:edit permissions')->only('edit','update');
        $this->middleware('permission:delete permissions')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $permissions = Permission::query();

            return datatables()
                ->of($permissions)
                ->addColumn('actions', function ($permission) {

                    return view(
                        'permissions.partials.actions',
                        compact('permission')
                    );

                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('permissions.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('permissions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Permission::create([
            'name' => $request->name
        ]);

        return redirect()
            ->route('permissions.index')
            ->with('success','Permiso creado');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Permission $permission)
    {
        return view(
            'permissions.edit',
            compact('permission')
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Permission $permission)
    {
        $permission->update([ 'name' => $request->name ]);

        return redirect()
            ->route('permissions.index')
            ->with('success','Permiso actualizado');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
    {
        $permission->delete();

        return redirect()
            ->route('permissions.index')
            ->with('success','Permiso eliminado');
    }
}
