<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth('api')->user() ?? $request->user();

        if (!$user || ($user->role ?? null) !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden (admin only)',
                'data' => null,
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
