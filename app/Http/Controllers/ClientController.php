<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Http\Requests\ClientRequest;
use App\Models\Ubigeo;


class ClientController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(
            Client::class,
            'client'
        );
    }

    public function index()
    {
        $clients = Client::latest()
            ->paginate(10);

        return view(
            'clients.index',
            compact('clients')
        );
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(ClientRequest $request)
    {
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
        return view(
            'clients.show',
            compact('client')
        );
    }

    public function edit(Client $client)
    {
        return view(
            'clients.edit',
            compact('client')
        );
    }

    public function update(
        ClientRequest $request,
        Client $client
    )
    {
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

}