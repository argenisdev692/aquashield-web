<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * Switch the application language
     *
     * @param string $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switchLang($locale)
    {
        // Validate the locale
        if (in_array($locale, ['en', 'es'])) {
            Session::put('locale', $locale);
        }

        return redirect()->back();
    }

    /**
     * Get available languages
     *
     * @return array
     */
    public function getAvailableLanguages()
    {
        return [
            'en' => [
                'name' => __('messages.english'),
                'flag' => '🇺🇸',
                'code' => 'en'
            ],
            'es' => [
                'name' => __('messages.spanish'),
                'flag' => '🇪🇸',
                'code' => 'es'
            ]
        ];
    }
} 