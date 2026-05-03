<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    
    public function __construct()
    {
        //$this->authorizeResource(Role::class, 'role');
        $this->middleware('permission:view roles')->only('index');
        $this->middleware('permission:create roles')->only('create','store');
        $this->middleware('permission:edit roles')->only('edit','update');
        $this->middleware('permission:delete roles')->only('destroy');
    }

    public function index(Request $request)
    {

        if ($request->ajax()) {

            $roles = Role::query();

            return datatables()
                ->of($roles)

                ->addColumn('permissions', function ($role) {

                    return $role
                        ->permissions
                        ->pluck('name')
                        ->implode(', ');

                })

                ->addColumn('actions', function ($role) {

                    return view(
                        'roles.partials.actions',
                        compact('role')
                    )->render();

                })

                ->rawColumns(['actions'])

                ->make(true);
        }

        return view('roles.index');
    }


    public function create()
    {

        $permissions =
            Permission::pluck('name','name');

        return view(
            'roles.create',
            compact('permissions')
        );
    }


    public function store(Request $request)
    {

        $role = Role::create([

            'name' => $request->name

        ]);

        $role->syncPermissions(
            $request->permissions
        );

        return redirect()
            ->route('roles.index')
            ->with('success','Rol creado');

    }


    public function edit(Role $role)
    {

        $permissions =
            Permission::pluck('name','name');

        $rolePermissions =
            $role->permissions
                ->pluck('name')
                ->toArray();

        return view(
            'roles.edit',
            compact(
                'role',
                'permissions',
                'rolePermissions'
            )
        );

    }


    public function update(
        Request $request,
        Role $role
    )
    {

        $role->update([
            'name' => $request->name
        ]);

        $role->syncPermissions(
            $request->permissions
        );

        return redirect()
            ->route('roles.index')
            ->with('success','Rol actualizado');

    }

}