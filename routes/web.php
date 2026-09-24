<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        // Stage 4: Manage Managers
    });

    // Manager & Admin routes
    Route::middleware('role:admin|manager')->group(function () {
        // Stage 5: Manage Receptionists & Clients
        // Stage 6: Manage Floors
        // Stage 7: Manage Rooms
        // Stage 13: Statistics
    });

    // Staff routes (Admin, Manager, Receptionist)
    Route::middleware('role:admin|manager|receptionist')->group(function () {
        // Stage 8: Client approvals
    });

    // Client routes
    Route::middleware('role:client')->group(function () {
        // Stage 9: Make reservations, view my reservations
    });
});

require __DIR__.'/auth.php';
