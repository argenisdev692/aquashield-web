<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;

class LanguageHelper
{
    /**
     * Get available languages
     */
    public static function getAvailableLanguages(): array
    {
        return [
            'en' => [
                'code' => 'en',
                'name' => 'English',
                'flag' => '🇺🇸',
                'flag_image' => 'assets/flags/us.svg'
            ],
            'es' => [
                'code' => 'es',
                'name' => 'Español',
                'flag' => '🇪🇸',
                'flag_image' => 'assets/flags/es.svg'
            ]
        ];
    }

    /**
     * Get current language
     */
    public static function getCurrentLanguage(): string
    {
        return App::getLocale();
    }

    /**
     * Get current language data
     */
    public static function getCurrentLanguageData(): array
    {
        $languages = self::getAvailableLanguages();
        $current = self::getCurrentLanguage();
        
        return $languages[$current] ?? $languages['en'];
    }

    /**
     * Set language
     */
    public static function setLanguage(string $language): void
    {
        $availableLanguages = array_keys(self::getAvailableLanguages());
        
        if (in_array($language, $availableLanguages)) {
            Session::put('locale', $language);
            App::setLocale($language);
        }
    }

    /**
     * Get language from session or default
     */
    public static function getLanguageFromSession(): string
    {
        return Session::get('locale', config('app.locale', 'en'));
    }

    /**
     * Get translation with fallback
     */
    public static function trans(string $key, array $replace = [], string $locale = null): string
    {
        $locale = $locale ?? self::getCurrentLanguage();
        
        // Try to get from messages file first
        $translation = __("messages.{$key}", $replace, $locale);
        
        // If not found in messages, try the key directly
        if ($translation === "messages.{$key}") {
            $translation = __($key, $replace, $locale);
        }
        
        // If still not found, try English as fallback
        if ($translation === $key && $locale !== 'en') {
            $translation = __("messages.{$key}", $replace, 'en');
            
            if ($translation === "messages.{$key}") {
                $translation = __($key, $replace, 'en');
            }
        }
        
        return $translation !== $key ? $translation : $key;
    }

    /**
     * Get RTL direction for language
     */
    public static function isRTL(string $language = null): bool
    {
        $language = $language ?? self::getCurrentLanguage();
        $rtlLanguages = ['ar', 'he', 'fa', 'ur'];
        
        return in_array($language, $rtlLanguages);
    }

    /**
     * Get language direction
     */
    public static function getDirection(string $language = null): string
    {
        return self::isRTL($language) ? 'rtl' : 'ltr';
    }

    /**
     * Generate URL with language parameter
     */
    public static function getLanguageUrl(string $language, string $route = null): string
    {
        $route = $route ?? request()->route()->getName();
        $parameters = request()->route()->parameters();
        
        // Add language parameter
        $parameters['lang'] = $language;
        
        try {
            return route($route, $parameters);
        } catch (\Exception $e) {
            return url()->current() . '?lang=' . $language;
        }
    }

    /**
     * Get language name by code
     */
    public static function getLanguageName(string $code): string
    {
        $languages = self::getAvailableLanguages();
        return $languages[$code]['name'] ?? $code;
    }

    /**
     * Get language flag by code
     */
    public static function getLanguageFlag(string $code): string
    {
        $languages = self::getAvailableLanguages();
        return $languages[$code]['flag'] ?? '🌐';
    }

    /**
     * Get language flag image by code
     */
    public static function getLanguageFlagImage(string $code): string
    {
        $languages = self::getAvailableLanguages();
        return $languages[$code]['flag_image'] ?? 'assets/flags/default.svg';
    }
} 