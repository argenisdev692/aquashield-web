<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\CompanyData;
use App\Models\EmailData;
use Illuminate\Support\Facades\View;

class ShareCompanyData
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Obtener los datos de la compañía
        $companyData = CompanyData::first();
        $emailData = EmailData::all();

        // Compartir los datos con todas las vistas
        View::share('companyData', $companyData);
        View::share('emailData', $emailData);

        return $next($request);
    }
} 