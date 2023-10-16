<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

class TokenValidationMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (env('AUTH_ENABLE') === true) {
            $token = $request->header('Authorization');

            $response = Http::withHeaders(['Authorization' => $token])
                ->get('https://auth.passcess.net/auth/realms/master/protocol/openid-connect/userinfo?client_id=pulsar-portal');

            if ($response->successful()) {
                return $next($request);
            } else {
                return response([
                    'message' => 'Error. Token is not valid.',
                    'keycloak_response' => $response->json(),
                ], $response->status());
            }
        } else {
            return $next($request);
        }
    }
}
