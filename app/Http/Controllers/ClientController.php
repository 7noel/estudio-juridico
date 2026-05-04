<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Http\Requests\ClientRequest;
use App\Models\Ubigeo;
use Illuminate\Http\Request;


class ClientController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(Client::class, 'client');
        $this->middleware('permission:view clients')->only('index');
        $this->middleware('permission:create clients')->only('create','store');
        $this->middleware('permission:edit clients')->only('edit','update');
        $this->middleware('permission:delete clients')->only('destroy');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $clients = Client::query();
            return datatables()
                ->of($clients)
                ->addColumn('document', function ($client) {
                    return $client->document_type_text.' '.$client->document_number;
                })
                ->addColumn('actions', function ($client) {
                    return view(
                        'clients.partials.actions',
                        compact('client')
                    )->render();
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('clients.index');
    }

    public function create()
    {
        // $this->authorize('create', Client::class);

        return view('clients.create');
    }

    public function store(ClientRequest $request)
    {
        // $this->authorize('create', Client::class);

        Client::create(
            $request->validated()
        );

        return redirect()
            ->route('clients.index')
            ->with(
                'success',
                'Cliente registrado correctamente.'
            );
    }

    public function show(Client $client)
    {
        // $this->authorize('view', Client::class);

        return view(
            'clients.show',
            compact('client')
        );
    }

    public function edit(Client $client)
    {
        $client->load('ubigeo');

        return view(
            'clients.edit',
            compact('client')
        );
    }

    public function update(ClientRequest $request, Client $client)
    {
        // $this->authorize('update', $client);

        $client->update(
            $request->validated()
        );

        return redirect()
            ->route('clients.index')
            ->with(
                'success',
                'Cliente actualizado.'
            );
    }

    public function destroy(Client $client)
    {
        // $this->authorize('delete', $client);

        $client->delete();

        return redirect()
            ->route('clients.index')
            ->with(
                'success',
                'Cliente eliminado.'
            );
    }


    public function searchUbigeo()
    {
        $term = request('term');
        return Ubigeo::query()->where('distrito','like',"%$term%")->orWhere('provincia','like',"%$term%")->orWhere('departamento','like',"%$term%")
            ->limit(100)->get()
            ->map(function ($u) {
                return [
                    'id' => $u->code,
                    'text' => $u->departamento .' - '. $u->provincia .' - '. $u->distrito
                ];

            });

    }

    public function search(Request $request)
    {
        return Client::where('full_name', 'like', "%{$request->q}%")
            ->limit(10)
            ->get()
            ->map(fn($c) => [
                'id' => $c->id,
                'label' => $c->full_name
            ]);
    }

}