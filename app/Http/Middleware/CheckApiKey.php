<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // dd('Middleware CheckApiKey invoked');
        // 1. Check for the API Key in the Authorization header
        $apiKey = $request->header('X-API-Key');

    
        $secretKey = env('API_KEY_SECRET');
        // dd($secretKey);
        // 2. Compare with the secret key stored in the .env file
        // FIX: Ensure that the secret key exists AND the received key matches it.
        if (empty($secretKey) || $apiKey !== $secretKey) {
            return response()->json([
                'message' => 'Unauthorized access. Invalid API Key.'
            ], 401);
        }
        return $next($request);
    }
}
