<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request; // Asegúrate que Request esté importado

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * @var array|string|null
     */
    // Opción 1: Confiar en todos los proxies (más simple para esta configuración)
    protected $proxies = '*';

    // Opción 2: Confiar específicamente en el localhost si Nginx está en la misma máquina
    // protected $proxies = '127.0.0.1';

    /**
     * The headers that should be used to detect proxies.
     *
     * @var int
     */
    // Asegúrate que X-Forwarded-Proto esté incluido (es el predeterminado moderno)
     protected $headers = Request::HEADER_X_FORWARDED_ALL;
}