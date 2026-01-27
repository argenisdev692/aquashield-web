<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        // Primero establecer un idioma por defecto
        $defaultLocale = config('app.locale', 'en');
        
        if (session()->has('locale')) {
            $locale = session()->get('locale');
            // Validar que el idioma sea soportado
            if (in_array($locale, ['en', 'es'])) {
                app()->setLocale($locale);
            } else {
                app()->setLocale($defaultLocale);
            }
        } else {
            app()->setLocale($defaultLocale);
        }
        
        return $next($request);
    }
}