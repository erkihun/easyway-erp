<?php
declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $allowedLocales = (array) config('localization.supported_locales', ['en', 'am']);
        $locale = (string) $request->session()->get('locale', config('localization.default_locale', config('app.locale', 'en')));

        if (!in_array($locale, $allowedLocales, true)) {
            $locale = (string) config('localization.fallback_locale', config('app.fallback_locale', 'en'));
        }

        App::setLocale($locale);

        return $next($request);
    }
}

