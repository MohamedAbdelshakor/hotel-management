<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreManagerRequest;
use App\Http\Requests\UpdateManagerRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ManagerController extends Controller
{
    /**
     * Display a listing of managers with server-side pagination and search.
     */
    public function index(Request $request): Response
    {
        $search = $request->input('search');

        $managers = User::role('manager')
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
            ]);

        return Inertia::render('Admin/Managers/Index', [
            'managers' => $managers,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    /**
     * Show the form for creating a new manager.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Managers/Create');
    }

    /**
     * Store a newly created manager.
     */
    public function store(StoreManagerRequest $request): RedirectResponse
    {
        $avatarPath = null;
        if ($request->hasFile('avatar_image')) {
            $avatarPath = $request->file('avatar_image')->store('avatars', 'public');
        }

        $manager = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'national_id' => $request->national_id,
            'avatar_image' => $avatarPath,
            'is_approved' => true,
            'email_verified_at' => now(),
        ]);

        $manager->assignRole('manager');

        return redirect()->route('admin.managers.index')
            ->with('success', 'Manager created successfully.');
    }

    /**
     * Show the form for editing a manager.
     */
    public function edit(User $manager): Response
    {
        abort_unless($manager->hasRole('manager'), 404);

        return Inertia::render('Admin/Managers/Edit', [
            'manager' => [
                'id' => $manager->id,
                'name' => $manager->name,
                'email' => $manager->email,
                'national_id' => $manager->national_id,
                'avatar_url' => $manager->avatar_url,
            ],
        ]);
    }

    /**
     * Update the specified manager.
     */
    public function update(UpdateManagerRequest $request, User $manager): RedirectResponse
    {
        abort_unless($manager->hasRole('manager'), 404);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'national_id' => $request->national_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        if ($request->hasFile('avatar_image')) {
            if ($manager->avatar_image && Storage::disk('public')->exists($manager->avatar_image)) {
                Storage::disk('public')->delete($manager->avatar_image);
            }
            $data['avatar_image'] = $request->file('avatar_image')->store('avatars', 'public');
        }

        $manager->update($data);

        return redirect()->route('admin.managers.index')
            ->with('success', 'Manager updated successfully.');
    }

    /**
     * Remove the specified manager.
     */
    public function destroy(Request $request, User $manager): JsonResponse|RedirectResponse
    {
        abort_unless($manager->hasRole('manager'), 404);

        if ($manager->floors()->exists() || $manager->rooms()->exists() || $manager->createdStaff()->exists()) {
            if ($request->expectsJson() || $request->header('X-Inertia')) {
                return back()->with('error', 'Cannot delete manager with associated floors, rooms, or staff.');
            }

            return redirect()->route('admin.managers.index')
                ->with('error', 'Cannot delete manager with associated floors, rooms, or staff.');
        }

        if ($manager->avatar_image && Storage::disk('public')->exists($manager->avatar_image)) {
            Storage::disk('public')->delete($manager->avatar_image);
        }

        $manager->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Manager deleted successfully.',
            ]);
        }

        return redirect()->route('admin.managers.index')
            ->with('success', 'Manager deleted successfully.');
    }
}
