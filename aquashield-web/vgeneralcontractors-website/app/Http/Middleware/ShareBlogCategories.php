<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Models\BlogCategory;

class ShareBlogCategories
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            // Intenta obtener categorías solo si la tabla existe
            $blogCategories = BlogCategory::all();
            View::share('blogCategories', $blogCategories);
        } catch (\Exception $e) {
            // Si hay algún error (p.ej. tabla no existe), simplemente continúa
            \Log::warning('Error al cargar categorías del blog: ' . $e->getMessage());
            View::share('blogCategories', collect([]));
        }
        
        return $next($request);
    }
} 