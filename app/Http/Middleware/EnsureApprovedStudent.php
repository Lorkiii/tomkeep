<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Blocks student-dashboard routes until the admin has approved the account.
 *
 * – pending  → redirected to the "waiting for approval" holding page
 * – rejected → redirected to the "application rejected" notice page
 * – approved → allowed through
 */
class EnsureApprovedStudent
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Admins are never gated by student approval status.
        if ($user->role === 'admin') {
            return $next($request);
        }

        if ($user->status === 'pending') {
            return redirect()->route('waiting-approval');
        }

        if ($user->status === 'rejected') {
            return redirect()->route('application-rejected');
        }

        return $next($request);
    }
}
