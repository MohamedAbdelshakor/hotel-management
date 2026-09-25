<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ClientController extends Controller
{
    /**
     * Display a listing of clients with server-side pagination and search.
     */
    public function index(Request $request): Response
    {
        Gate::authorize('viewAny', User::class);

        $search = $request->input('search');

        $clients = User::role('client')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('mobile', 'like', "%{$search}%")
                        ->orWhere('country', 'like', "%{$search}%")
                        ->orWhere('national_id', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString()
            ->through(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'mobile' => $user->mobile,
                'country' => $user->country,
                'gender' => $user->gender,
                'national_id' => $user->national_id,
                'is_approved' => (bool) $user->is_approved,
                'avatar_url' => $user->avatar_url,
                'created_at' => $user->created_at?->format('Y-m-d'),
                'can' => [
                    'update' => $request->user()->can('update', $user),
                    'delete' => $request->user()->can('delete', $user),
                ],
            ]);

        return Inertia::render('Clients/Index', [
            'clients' => $clients,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    /**
     * Show the form for creating a new client.
     */
    public function create(): Response
    {
        Gate::authorize('create', User::class);

        return Inertia::render('Clients/Create');
    }

    /**
     * Store a newly created client.
     */
    public function store(StoreClientRequest $request): RedirectResponse
    {
        $avatarPath = null;
        if ($request->hasFile('avatar_image')) {
            $avatarPath = $request->file('avatar_image')->store('avatars', 'public');
        }

        $client = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'mobile' => $request->mobile,
            'country' => $request->country,
            'gender' => $request->gender,
            'national_id' => $request->national_id,
            'avatar_image' => $avatarPath,
            'created_by_id' => $request->user()->id,
            'is_approved' => true,
            'approved_by_id' => $request->user()->id,
            'approved_at' => now(),
            'email_verified_at' => now(),
        ]);

        $client->assignRole('client');

        return redirect()->route('clients.index')
            ->with('success', 'Client created successfully.');
    }

    /**
     * Show the form for editing a client.
     */
    public function edit(User $client): Response
    {
        abort_unless($client->hasRole('client'), 404);
        Gate::authorize('update', $client);

        return Inertia::render('Clients/Edit', [
            'client' => [
                'id' => $client->id,
                'name' => $client->name,
                'email' => $client->email,
                'mobile' => $client->mobile,
                'country' => $client->country,
                'gender' => $client->gender,
                'national_id' => $client->national_id,
                'avatar_url' => $client->avatar_url,
            ],
        ]);
    }

    /**
     * Update the specified client.
     */
    public function update(UpdateClientRequest $request, User $client): RedirectResponse
    {
        abort_unless($client->hasRole('client'), 404);
        Gate::authorize('update', $client);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'country' => $request->country,
            'gender' => $request->gender,
            'national_id' => $request->national_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        if ($request->hasFile('avatar_image')) {
            if ($client->avatar_image && Storage::disk('public')->exists($client->avatar_image)) {
                Storage::disk('public')->delete($client->avatar_image);
            }
            $data['avatar_image'] = $request->file('avatar_image')->store('avatars', 'public');
        }

        $client->update($data);

        return redirect()->route('clients.index')
            ->with('success', 'Client updated successfully.');
    }

    /**
     * Remove the specified client.
     */
    public function destroy(Request $request, User $client): JsonResponse|RedirectResponse
    {
        abort_unless($client->hasRole('client'), 404);
        Gate::authorize('delete', $client);

        if ($client->reservations()->exists()) {
            if ($request->expectsJson() || $request->header('X-Inertia')) {
                return back()->with('error', 'Cannot delete client with existing reservations.');
            }

            return redirect()->route('clients.index')
                ->with('error', 'Cannot delete client with existing reservations.');
        }

        if ($client->avatar_image && Storage::disk('public')->exists($client->avatar_image)) {
            Storage::disk('public')->delete($client->avatar_image);
        }

        $client->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Client deleted successfully.',
            ]);
        }

        return redirect()->route('clients.index')
            ->with('success', 'Client deleted successfully.');
    }
}
