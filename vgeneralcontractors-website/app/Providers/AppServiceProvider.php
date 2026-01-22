<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Str;
use App\Helpers\StringHelper;
use App\Services\FacebookConversionApi;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use App\Http\Controllers\CompanyDataController;
use App\Repositories\Interfaces\InsuranceCompanyRepositoryInterface;
use App\Repositories\InsuranceCompanyRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->singleton(FacebookConversionApi::class, function ($app) {
            return new FacebookConversionApi();
        });

        // Repository bindings
        $this->app->bind(InsuranceCompanyRepositoryInterface::class, InsuranceCompanyRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Definir rate limiters
        $this->configureRateLimiting();
        
        // Monitoreo de jobs
        Queue::before(function (JobProcessing $event) {
            try {
                $payload = $event->job->payload();
                if (isset($payload['data']) && isset($payload['data']['command'])) {
                    $command = $payload['data']['command'];
                    $jobClass = is_object($command) ? get_class($command) : (is_string($command) ? $command : 'Unknown');
                } else {
                    $jobClass = 'Unknown (malformed payload)';
                }
                
                \Log::info('Processing job', [
                    'job' => $jobClass,
                    'queue' => $event->job->getQueue()
                ]);
            } catch (\Throwable $e) {
                \Log::error('Error logging job processing: ' . $e->getMessage());
            }
        });

        Queue::after(function (JobProcessed $event) {
            try {
                $payload = $event->job->payload();
                if (isset($payload['data']) && isset($payload['data']['command'])) {
                    $command = $payload['data']['command'];
                    $jobClass = is_object($command) ? get_class($command) : (is_string($command) ? $command : 'Unknown');
                } else {
                    $jobClass = 'Unknown (malformed payload)';
                }
                
                \Log::info('Job processed', [
                    'job' => $jobClass,
                    'queue' => $event->job->getQueue()
                ]);
            } catch (\Throwable $e) {
                \Log::error('Error logging job completion: ' . $e->getMessage());
            }
        });

        Str::macro('readDuration', function ($content, $wordsPerMinute = 200) {
            return StringHelper::readDuration($content, $wordsPerMinute);
        });
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        // API rate limiter
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // For contact forms - limit to 3 submissions per minute
        RateLimiter::for('contact', function (Request $request) {
            return Limit::perMinute(3)->by($request->ip())
                ->response(function () {
                    return response()->json([
                        'success' => false,
                        'message' => 'Too many requests. Please try again in a moment.'
                    ], 429);
                });
        });

        // For field validation - allow more frequent but still limited
        RateLimiter::for('validation', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        // Global protection for all routes
        RateLimiter::for('global', function (Request $request) {
            return Limit::perMinute(300)->by($request->ip());
        });
    }
}
