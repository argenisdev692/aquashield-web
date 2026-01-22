<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Repository Interfaces
use App\Repositories\Interfaces\ClaimRepositoryInterface;

// Repository Implementations
use App\Repositories\Claims\ClaimRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind repository interfaces to their implementations
        $this->app->bind(ClaimRepositoryInterface::class, ClaimRepository::class);
        
        // Add more repository bindings here as you create them
        // $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        // $this->app->bind(AppointmentRepositoryInterface::class, AppointmentRepository::class);
        // $this->app->bind(PostRepositoryInterface::class, PostRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
} 