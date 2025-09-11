<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PortalBasicAuth
{
    /**
     * Handle an incoming request.
     * Applies HTTP Basic Authentication using credentials from .env
     * BASIC_AUTH_USER and BASIC_AUTH_PASS.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $expectedUser = env('BASIC_AUTH_USER');
        $expectedPass = env('BASIC_AUTH_PASS');

        // If credentials are not configured, always challenge
        if ($expectedUser === null || $expectedPass === null) {
            return response('Authentication not configured', 401, [
                'WWW-Authenticate' => 'Basic realm="Privado"'
            ]);
        }

        // Prefer framework helpers
        $providedUser = $request->getUser();
        $providedPass = $request->getPassword();

        // Fallback: parse Authorization header if needed (some proxies/FastCGI setups)
        if ($providedUser === null || $providedPass === null) {
            $auth = $request->header('Authorization') ?? $request->server('HTTP_AUTHORIZATION');
            if ($auth && str_starts_with($auth, 'Basic ')) {
                $decoded = base64_decode(substr($auth, 6));
                if ($decoded !== false) {
                    [$u, $p] = array_pad(explode(':', $decoded, 2), 2, null);
                    $providedUser = $providedUser ?? $u;
                    $providedPass = $providedPass ?? $p;
                }
            }
        }

        if ($providedUser !== $expectedUser || $providedPass !== $expectedPass) {
            return response('Unauthorized', 401, [
                'WWW-Authenticate' => 'Basic realm="Privado"'
            ]);
        }

        return $next($request);
    }
}
