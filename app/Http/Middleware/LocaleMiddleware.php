<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LocaleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $defaultLocale = config('app.locale', 'es');
        $locale = session('locale', $defaultLocale);

        if (in_array($locale, ['en', 'es'], true)) {
            app()->setLocale($locale);
        }
        return $next($request);
    }
}
