<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
	public function handle(Request $request, Closure $next): Response
	{
		// Read the currently authenticated user from the request.
		$user = $request->user();

		// Guests are always sent to the login page.
		if (! $user) {
			return redirect()->route('login');
		}

		// Non-admin users are redirected back to the correct student flow.
		if ($user->role !== 'admin') {
			$target = $user->profile_completed ? 'home' : 'profile.setup';

			return redirect()->route($target);
		}

		// Only true admins are allowed to continue into the admin route group.
		return $next($request);
	}
}
