<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureNotBanned
{
    /**
     * Block banned users from authenticated areas.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->isBanned()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Your account is temporarily banned.',
                    'banned_until' => $user->banned_until?->toIso8601String(),
                ], 403);
            }

            abort(403, 'Your account is temporarily banned until '.$user->banned_until?->toDateTimeString().'.');
        }

        return $next($request);
    }
}
