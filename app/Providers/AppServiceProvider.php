<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Image\ImageCompressor;
use App\Services\SmartphoneImageService;
use App\Services\Supabase\SupabaseStorageService;

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
        //
    }
}