<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ContentSecurityPolicy
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
   public function handle(Request $request, Closure $next): Response
{
    $response = $next($request);

    // Questa configurazione bypassa i blocchi rigidi solo per i test in locale
    $csp = "default-src * 'unsafe-inline' 'unsafe-eval' data: blob:; " .
           "script-src * 'unsafe-inline' 'unsafe-eval'; " .
           "style-src * 'unsafe-inline'; " .
           "img-src * data: blob:; " .
           "connect-src * ws://* wss://*;";

    $response->headers->set('Content-Security-Policy', $csp);

    return $response;
}
}

