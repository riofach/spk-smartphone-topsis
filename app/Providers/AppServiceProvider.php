<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Image\ImageCompressor;
use App\Services\SmartphoneImageService;
use App\Services\Supabase\SupabaseStorageService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register ImageCompressor service
        $this->app->singleton(ImageCompressor::class, function () {
            return new ImageCompressor();
        });

        // Register SupabaseStorageService
        $this->app->singleton(SupabaseStorageService::class, function () {
            return new SupabaseStorageService();
        });

        // Register SmartphoneImageService
        $this->app->singleton(SmartphoneImageService::class, function ($app) {
            return new SmartphoneImageService(
                $app->make(ImageCompressor::class),
                $app->make(SupabaseStorageService::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS in production
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // Set default string length for schema
        Schema::defaultStringLength(191);

        // Disable wrapping of JSON resources
        JsonResource::withoutWrapping();

        // Prevent lazy loading in production for better performance
        Model::preventLazyLoading(!app()->isProduction());

        // Always make sure the cache directory exists
        if (!file_exists(storage_path('framework/cache'))) {
            mkdir(storage_path('framework/cache'), 0777, true);
        }
    }
}