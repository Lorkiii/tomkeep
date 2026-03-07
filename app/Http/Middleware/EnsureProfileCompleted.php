<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Redirects authenticated users to profile setup until required profile data is completed.
 */
class EnsureProfileCompleted
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! $user->profile_completed && ! $request->routeIs('profile.setup')) {
            return redirect()->route('profile.setup');
        }

        return $next($request);
    }
}
