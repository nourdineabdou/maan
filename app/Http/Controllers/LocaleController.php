<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function switch(Request $request, string $locale): RedirectResponse
    {
        $supported = config('localization.supported_locales', ['fr']);

        if (in_array($locale, $supported, true)) {
            session(['locale' => $locale]);

            if ($request->user()) {
                $request->user()->update(['preferred_locale' => $locale]);
            }
        }

        return redirect()->back();
    }
}
