<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class DetectDevice
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $userAgent = $request->header('User-Agent');

        // Tentukan prefix berdasarkan User Agent
        if (strpos($userAgent, 'Helpdesk-Mobile') !== false) {
            // Untuk mobile (Android app)
            View::addNamespace('theme', resource_path('views/mobile'));
            config(['device.theme' => 'mobile']);
        } else {
            // Untuk web (desktop)
            View::addNamespace('theme', resource_path('views'));
            config(['device.theme' => 'web']);
        }

        return $next($request);
    }
}
