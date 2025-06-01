<?php

namespace App\Console;

use App\Console\Commands\MigrateSmartphoneImages;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * These schedules are run in a default environment.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();

        // Membersihkan smartphone usang setiap hari pada pukul 00:00
        $schedule->command('smartphones:cleanup-obsolete')->daily()->at('00:00')
            ->onSuccess(function () {
                Log::info('Smartphone obsolete cleanup succeeded');
            })
            ->onFailure(function () {
                Log::error('Smartphone obsolete cleanup failed');
            });

        // Membersihkan cache view setiap 12 jam
        $schedule->command('view:clear')->twiceDaily(1, 13)
            ->appendOutputTo(storage_path('logs/schedule-view-clear.log'));

        // Membersihkan cache secara mingguan
        $schedule->command('optimize:clear')->weekly()
            ->appendOutputTo(storage_path('logs/schedule-optimize.log'));

        // Membersihkan cache spesifik untuk smartphone dan meregenerasi sitemap setiap 6 jam
        $schedule->call(function () {
            // Regenerate sitemap.xml
            try {
                Log::info('Regenerating sitemap cache');
                \Illuminate\Support\Facades\Cache::forget('sitemap.xml');
                // Hitting the sitemap URL to regenerate the cache
                file_get_contents(url('/sitemap.xml'));
            } catch (\Exception $e) {
                Log::error('Error regenerating sitemap: ' . $e->getMessage());
            }

            // Regenerate smartphones data
            try {
                Log::info('Regenerating smartphone caches');
                \App\Models\Smartphone::clearModelCache();
            } catch (\Exception $e) {
                Log::error('Error clearing smartphone cache: ' . $e->getMessage());
            }
        })->everySixHours()
            ->name('regenerate-sitemap-and-model-cache')
            ->appendOutputTo(storage_path('logs/schedule-cache.log'));

        // Verifikasi performa website daily
        $schedule->call(function () {
            try {
                Log::info('Performance check: pinging website');
                file_get_contents(url('/'));
            } catch (\Exception $e) {
                Log::error('Website is not responding: ' . $e->getMessage());
            }
        })->hourly()
            ->between('8:00', '22:00') // Hanya pada jam aktif
            ->name('website-health-check');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        MigrateSmartphoneImages::class,
    ];
}