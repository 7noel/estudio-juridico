<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Establishment;
use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
        $this->middleware('permission:view users')->only('index');
        $this->middleware('permission:create users')->only('create','store');
        $this->middleware('permission:edit users')->only('edit','update');
        $this->middleware('permission:delete users')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $users = User::query();
            return datatables()
                ->of($users)
                ->addColumn('role', function ($user) {
                    return $user->getRoleNames()->first() ?? '-';
                })
                ->addColumn('actions', function ($user) {
                    return view(
                        'users.partials.actions',
                        compact('user')
                    )->render();
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('users.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $establishments = Establishment::pluck('name','id');
        $roles = Role::pluck('name','name');
        $user = new User();

        return view('users.create', compact('roles', 'establishments', 'user'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = User::create([
            'name' => $request->name,
            'establishment_id' => $request->establishment_id,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'password' => \Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        return redirect()
            ->route('users.index')
            ->with('success','Usuario creado');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $establishments = Establishment::pluck('name','id');
        $roles = Role::pluck('name','name');

        return view('users.edit', compact('user', 'establishments','roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, User $user)
    {
        $data = [
            'name' => $request->name,
            'establishment_id' => $request->establishment_id,
            'mobile' => $request->mobile,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = \Hash::make($request->password);
        }
        $user->update($data);
        $user->syncRoles($request->role);

        return redirect()
            ->route('users.index')
            ->with('success','Usuario actualizado');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
