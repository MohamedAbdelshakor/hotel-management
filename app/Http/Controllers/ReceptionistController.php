<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReceptionistRequest;
use App\Http\Requests\UpdateReceptionistRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ReceptionistController extends Controller
{
    /**
     * Display a listing of receptionists with server-side pagination and search.
     */
    public function index(Request $request): Response
    {
        Gate::authorize('viewAny', User::class);

        $search = $request->input('search');

        $receptionists = User::role('receptionist')
            ->with('creator')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
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
                'national_id' => $user->national_id,
                'avatar_url' => $user->avatar_url,
                'created_at' => $user->created_at?->format('Y-m-d'),
                'is_banned' => $user->isBanned(),
                'manager_name' => $user->creator?->name ?? 'Admin',
                'can' => [
                    'update' => $request->user()->can('update', $user),
                    'delete' => $request->user()->can('delete', $user),
                    'ban' => $request->user()->can('ban', $user),
                ],
            ]);

        return Inertia::render('Receptionists/Index', [
            'receptionists' => $receptionists,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    /**
     * Show the form for creating a new receptionist.
     */
    public function create(): Response
    {
        Gate::authorize('create', User::class);

        return Inertia::render('Receptionists/Create');
    }

    /**
     * Store a newly created receptionist.
     */
    public function store(StoreReceptionistRequest $request): RedirectResponse
    {
        $avatarPath = null;
        if ($request->hasFile('avatar_image')) {
            $avatarPath = $request->file('avatar_image')->store('avatars', 'public');
        }

        $receptionist = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'national_id' => $request->national_id,
            'avatar_image' => $avatarPath,
            'created_by_id' => $request->user()->id,
            'is_approved' => true,
            'email_verified_at' => now(),
        ]);

        $receptionist->assignRole('receptionist');

        return redirect()->route('receptionists.index')
            ->with('success', 'Receptionist created successfully.');
    }

    /**
     * Show the form for editing a receptionist.
     */
    public function edit(User $receptionist): Response
    {
        abort_unless($receptionist->hasRole('receptionist'), 404);
        Gate::authorize('update', $receptionist);

        return Inertia::render('Receptionists/Edit', [
            'receptionist' => [
                'id' => $receptionist->id,
                'name' => $receptionist->name,
                'email' => $receptionist->email,
                'national_id' => $receptionist->national_id,
                'avatar_url' => $receptionist->avatar_url,
            ],
        ]);
    }

    /**
     * Update the specified receptionist.
     */
    public function update(UpdateReceptionistRequest $request, User $receptionist): RedirectResponse
    {
        abort_unless($receptionist->hasRole('receptionist'), 404);
        Gate::authorize('update', $receptionist);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'national_id' => $request->national_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        if ($request->hasFile('avatar_image')) {
            if ($receptionist->avatar_image && Storage::disk('public')->exists($receptionist->avatar_image)) {
                Storage::disk('public')->delete($receptionist->avatar_image);
            }
            $data['avatar_image'] = $request->file('avatar_image')->store('avatars', 'public');
        }

        $receptionist->update($data);

        return redirect()->route('receptionists.index')
            ->with('success', 'Receptionist updated successfully.');
    }

    /**
     * Remove the specified receptionist.
     */
    public function destroy(Request $request, User $receptionist): JsonResponse|RedirectResponse
    {
        abort_unless($receptionist->hasRole('receptionist'), 404);
        Gate::authorize('delete', $receptionist);

        if ($receptionist->approvedClients()->exists()) {
            if ($request->expectsJson() || $request->header('X-Inertia')) {
                return back()->with('error', 'Cannot delete receptionist who has approved clients.');
            }

            return redirect()->route('receptionists.index')
                ->with('error', 'Cannot delete receptionist who has approved clients.');
        }

        if ($receptionist->avatar_image && Storage::disk('public')->exists($receptionist->avatar_image)) {
            Storage::disk('public')->delete($receptionist->avatar_image);
        }

        $receptionist->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Receptionist deleted successfully.',
            ]);
        }

        return redirect()->route('receptionists.index')
            ->with('success', 'Receptionist deleted successfully.');
    }

    /**
     * Ban the specified receptionist.
     */
    public function ban(Request $request, User $receptionist): RedirectResponse
    {
        abort_unless($receptionist->hasRole('receptionist'), 404);
        Gate::authorize('ban', $receptionist);

        $receptionist->ban();

        return back()->with('success', 'Receptionist banned successfully.');
    }

    /**
     * Unban the specified receptionist.
     */
    public function unban(Request $request, User $receptionist): RedirectResponse
    {
        abort_unless($receptionist->hasRole('receptionist'), 404);
        Gate::authorize('unban', $receptionist);

        $receptionist->unban();

        return back()->with('success', 'Receptionist unbanned successfully.');
    }
}
