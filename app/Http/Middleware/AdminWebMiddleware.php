<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminWebMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user || ($user->role ?? null) !== 'admin') {
            return redirect()->route('dashboard')->with('status', 'Akses admin diperlukan.');
        }

        return $next($request);
    }
}
