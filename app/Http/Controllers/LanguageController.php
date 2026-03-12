<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class LanguageController extends Controller
{
    public function switch(Request $request, string $locale): RedirectResponse
    {
        $allowedLocales = (array) config('localization.supported_locales', ['en', 'am']);

        if (!in_array($locale, $allowedLocales, true)) {
            $locale = (string) config('localization.fallback_locale', config('app.fallback_locale', 'en'));
        }

        $request->session()->put('locale', $locale);

        return back()->with('status', __('messages.language_changed'));
    }
}

