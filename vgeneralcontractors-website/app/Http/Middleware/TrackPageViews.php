<?php

namespace App\Http\Middleware;

use App\Services\FacebookConversionApi;
use Closure;

class TrackPageViews
{
    protected $fbApi;
    
    public function __construct(FacebookConversionApi $fbApi)
    {
        $this->fbApi = $fbApi;
    }
    
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        
        // Solo rastrear GET requests y páginas HTML (no assets)
        if ($request->isMethod('GET') && !$request->expectsJson() && !$request->ajax()) {
            $userData = [];
            
            // Si el usuario está autenticado, envía sus datos
            if (auth()->check()) {
                $user = auth()->user();
                $userData = [
                    'email' => $user->email,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                ];
            }
            
            try {
                $pageInfo = $this->getPageInfo($request);
                
                $this->fbApi->pageView($userData, [
                    'content_name' => $pageInfo['name'],
                    'content_category' => $pageInfo['category'],
                    'content_type' => 'page_view',
                    'content_id' => $pageInfo['id']
                ]);
            } catch (\Exception $e) {
                \Log::error('Facebook PageView Error: ' . $e->getMessage());
            }
        }
        
        return $response;
    }

    private function getPageInfo($request)
    {
        $path = $request->path();
        $segments = explode('/', $path);
        $mainSegment = $segments[0] ?? 'home';

        // Mapeo de páginas principales
        $pageMap = [
            // Servicios Específicos de Roofing
            'new-roof' => ['name' => 'New Roof Service', 'category' => 'Services', 'id' => 'new_roof'],
            'roof-repair' => ['name' => 'Roof Repair Service', 'category' => 'Services', 'id' => 'roof_repair'],
            'storm-damage' => ['name' => 'Storm Damage Service', 'category' => 'Services', 'id' => 'storm_damage'],
            'hail-damage' => ['name' => 'Hail Damage Service', 'category' => 'Services', 'id' => 'hail_damage'],
            
            // Servicios Generales
            'services' => ['name' => 'Services Page', 'category' => 'Services', 'id' => 'services_main'],
            'virtual-remodeler' => ['name' => 'Virtual Remodeler', 'category' => 'Services', 'id' => 'virtual_remodeler'],
            'insurance-claims' => ['name' => 'Insurance Claims', 'category' => 'Services', 'id' => 'insurance_claims'],
            'warranties' => ['name' => 'Warranties', 'category' => 'Services', 'id' => 'warranties'],
            'financing' => ['name' => 'Financing', 'category' => 'Services', 'id' => 'financing'],
            
            // Páginas Informativas
            'about' => ['name' => 'About', 'category' => 'Company', 'id' => 'about'],
            'faqs' => ['name' => 'FAQs', 'category' => 'Support', 'id' => 'faqs'],
            
            // Páginas de Contacto
            'contact-support' => ['name' => 'Contact Support', 'category' => 'Support', 'id' => 'contact_support'],
            'contact-form' => ['name' => 'Contact Form', 'category' => 'Conversion', 'id' => 'contact'],
            
            // Portfolio
            'portfolio' => ['name' => 'Portfolio', 'category' => 'Portfolio', 'id' => 'portfolio'],
            'portfolios' => ['name' => 'Portfolios Gallery', 'category' => 'Portfolio', 'id' => 'portfolios'],
            
            // Blog y Contenido
            'blog' => ['name' => 'Blog', 'category' => 'Content', 'id' => 'blog'],
            'posts' => ['name' => 'Blog Posts', 'category' => 'Content', 'id' => 'posts'],
            'blog-categories' => ['name' => 'Blog Categories', 'category' => 'Content', 'id' => 'blog_categories'],
            
            // Páginas Legales
            'privacy-policy' => ['name' => 'Privacy Policy', 'category' => 'Legal', 'id' => 'privacy'],
            'terms-and-conditions' => ['name' => 'Terms and Conditions', 'category' => 'Legal', 'id' => 'terms'],
            'cookies-policy' => ['name' => 'Cookies Policy', 'category' => 'Legal', 'id' => 'cookies'],
            
            // Productos
            'products' => ['name' => 'Products', 'category' => 'Products', 'id' => 'products'],
            
            // Admin y Dashboard
            'dashboard' => ['name' => 'Dashboard', 'category' => 'Admin', 'id' => 'dashboard'],
            'users' => ['name' => 'Users Management', 'category' => 'Admin', 'id' => 'users'],
            'company-data' => ['name' => 'Company Data', 'category' => 'Admin', 'id' => 'company_data'],
            'email-datas' => ['name' => 'Email Data', 'category' => 'Admin', 'id' => 'email_data'],
            'service-categories' => ['name' => 'Service Categories', 'category' => 'Admin', 'id' => 'service_categories'],
            
            // Autenticación
            'google-auth' => ['name' => 'Google Authentication', 'category' => 'Auth', 'id' => 'google_auth'],
            
            // Página principal
            'home' => ['name' => 'Homepage', 'category' => 'Main', 'id' => 'home'],
        ];

        // Manejo de rutas especiales
        if (str_contains($path, 'blog/search')) {
            return [
                'name' => 'Blog Search',
                'category' => 'Content',
                'id' => 'blog_search'
            ];
        }

        if (str_contains($path, 'blog/category/')) {
            return [
                'name' => 'Blog Category',
                'category' => 'Content',
                'id' => 'blog_category_' . ($segments[2] ?? 'all')
            ];
        }

        if (str_contains($path, 'blog/') && count($segments) > 1) {
            return [
                'name' => 'Blog Post',
                'category' => 'Content',
                'id' => 'blog_post_' . $segments[1]
            ];
        }

        // Retorna la información de la página o un valor por defecto
        return $pageMap[$mainSegment] ?? [
            'name' => ucfirst($mainSegment),
            'category' => 'Other',
            'id' => 'page_' . $mainSegment
        ];
    }
} 