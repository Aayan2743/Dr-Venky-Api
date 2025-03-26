<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;

class EnsureValidToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            // This will automatically check the token validity
            $request->user(); // If token is invalid, this will throw AuthenticationException
        } catch (AuthenticationException $e) {
            // If the token is invalid, handle it here
            return response()->json([
                'message' => 'Unauthenticated. Invalid or expired token.',
            ], 401);
        }

        return $next($request);
        
    }
}
