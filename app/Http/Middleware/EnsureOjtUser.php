<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOjtUser
{
    /**
     * Redirect to login if no OJT user is in session (local storage / no DB).
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('ojt_user_id')) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
