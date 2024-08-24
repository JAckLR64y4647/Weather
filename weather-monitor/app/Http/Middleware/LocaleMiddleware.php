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
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
		$locale = strtolower(substr($request->header('Accept-Language'), 0, 2));

		if (!in_array($locale, ['en', 'uk', 'ja'])) {
			$locale = 'en';
		}

		if ($locale) {
			app()->setLocale($locale);
		}

        return $next($request);
    }
}
