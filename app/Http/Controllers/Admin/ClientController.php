<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreClientRequest;
use App\Http\Requests\Admin\UpdateClientRequest;
use App\Models\Client;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function __construct(
        private NotificationService $notificationService,
    ) {}

    public function index(): View
    {
        $clients = Client::withCount('jobs')->latest()->paginate(15);

        return view('admin.clients.index', [
            'clients' => $clients,
            'unreadNotifications' => $this->notificationService->unreadCount(),
        ]);
    }

    public function create(): View
    {
        return view('admin.clients.create', [
            'unreadNotifications' => $this->notificationService->unreadCount(),
        ]);
    }

    public function store(StoreClientRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('logo');

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('clients', 'public');
        }

        $client = Client::create($data);

        return redirect()
            ->route('admin.clients.show', $client)
            ->with('success', 'Client created successfully.');
    }

    public function show(Client $client): View
    {
        $client->load(['jobs' => fn ($q) => $q->latest()->limit(10)]);

        return view('admin.clients.show', [
            'client' => $client,
            'unreadNotifications' => $this->notificationService->unreadCount(),
        ]);
    }

    public function edit(Client $client): View
    {
        return view('admin.clients.edit', [
            'client' => $client,
            'unreadNotifications' => $this->notificationService->unreadCount(),
        ]);
    }

    public function update(UpdateClientRequest $request, Client $client): RedirectResponse
    {
        $data = $request->safe()->except(['logo', 'remove_logo']);

        if ($request->boolean('remove_logo') && $client->logo_path) {
            Storage::disk('public')->delete($client->logo_path);
            $data['logo_path'] = null;
        }

        if ($request->hasFile('logo')) {
            if ($client->logo_path) {
                Storage::disk('public')->delete($client->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('clients', 'public');
        }

        $client->update($data);

        return redirect()
            ->route('admin.clients.show', $client)
            ->with('success', 'Client updated successfully.');
    }

    public function destroy(Client $client): RedirectResponse
    {
        if ($client->logo_path) {
            Storage::disk('public')->delete($client->logo_path);
        }

        $client->delete();

        return redirect()
            ->route('admin.clients.index')
            ->with('success', 'Client deleted successfully.');
    }
}
