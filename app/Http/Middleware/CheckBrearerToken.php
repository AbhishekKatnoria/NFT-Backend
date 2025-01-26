<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Sanctum\PersonalAccessToken;
use Carbon\Carbon;

class CheckBearerToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the Authorization header
        $authHeader = $request->header('Authorization');

        // Check if the Authorization header is present and properly formatted
        if ($authHeader && preg_match('/^Bearer\s(.+)$/', $authHeader, $matches)) {
            $tokenString = $matches[1];

            // Find the token in the database
            $token = PersonalAccessToken::findToken($tokenString);
            
            if ($token && $token->tokenable) {
                // Check if the token is expired
                if ($token->expires_at && Carbon::now()->greaterThan($token->expires_at)) {
                    // Token has expired, remove it from the database
                    $token->delete();

                    return response()->json([
                        'status' => false,
                        'error' => 'Session expired.'
                    ], 401);
                }

                // Token is valid and associated user exists
                $request->attributes->set('tokenableUser', $token->tokenable);
                return $next($request);
            } else {
                // Token is invalid or user not found
                return response()->json([
                    'status' => false,
                    'error' => 'Invalid token or user not found.'
                ], 401);
            }
            
        } else {
            // Authorization header is missing or improperly formatted
            return response()->json([
                'status' => false,
                'error' => 'Unauthorized access.'
            ], 401);
        }
    }
}