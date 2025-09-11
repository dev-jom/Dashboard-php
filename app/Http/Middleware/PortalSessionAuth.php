<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PortalSessionAuth
{
    /**
     * Require a session flag before accessing private pages.
     * If not authenticated, redirect to the login page with intended URL.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->session()->get('portal_authed')) {
            // Save intended URL and redirect to login
            return redirect()->route('portal.login', [
                'intended' => $request->fullUrl()
            ]);
        }

        return $next($request);
    }
}
