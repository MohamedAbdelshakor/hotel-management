<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureClientIsApproved
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->hasRole('client') && ! $user->is_approved) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Your account is pending approval by the hotel administration.',
                ], 403);
            }

            return redirect()->route('dashboard')->with('warning', 'Your account is pending approval by the hotel administration.');
        }

        return $next($request);
    }
}
