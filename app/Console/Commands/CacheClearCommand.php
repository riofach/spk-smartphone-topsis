<?php

namespace App\Console\Commands;

use App\Models\Smartphone;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CacheClearCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:smartclear {--all : Bersihkan semua cache} {--model : Bersihkan cache model saja} {--view : Bersihkan cache view saja} {--sitemap : Regenerasi sitemap}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bersihkan cache khusus untuk aplikasi RecomHp';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai pembersihan cache...');

        $all = $this->option('all');
        $model = $this->option('model');
        $view = $this->option('view');
        $sitemap = $this->option('sitemap');

        if ($all || $model) {
            $this->clearModelCache();
        }

        if ($all || $view) {
            $this->clearViewCache();
        }

        if ($all || $sitemap) {
            $this->regenerateSitemap();
        }

        if (!$all && !$model && !$view && !$sitemap) {
            $this->comment('Tidak ada opsi yang dipilih. Gunakan --all, --model, --view, atau --sitemap');

            // Tanyakan apakah ingin menjalankan semua
            if ($this->confirm('Apakah Anda ingin membersihkan semua cache?')) {
                $this->clearModelCache();
                $this->clearViewCache();
                $this->regenerateSitemap();
            }
        }

        $this->info('Pembersihan cache selesai!');

        return Command::SUCCESS;
    }

    /**
     * Bersihkan cache model (smartphone)
     */
    protected function clearModelCache()
    {
        $this->info('Membersihkan cache model...');

        try {
            Smartphone::clearModelCache();
            $this->info('✓ Cache model berhasil dihapus');
            Log::info('Cache model berhasil dihapus via command');
        } catch (\Exception $e) {
            $this->error('✗ Gagal membersihkan cache model: ' . $e->getMessage());
            Log::error('Gagal membersihkan cache model: ' . $e->getMessage());
        }
    }

    /**
     * Bersihkan cache view
     */
    protected function clearViewCache()
    {
        $this->info('Membersihkan cache view...');

        try {
            Artisan::call('view:clear');
            $this->info('✓ Cache view berhasil dihapus');
            Log::info('Cache view berhasil dihapus via command');
        } catch (\Exception $e) {
            $this->error('✗ Gagal membersihkan cache view: ' . $e->getMessage());
            Log::error('Gagal membersihkan cache view: ' . $e->getMessage());
        }
    }

    /**
     * Regenerasi sitemap
     */
    protected function regenerateSitemap()
    {
        $this->info('Meregenerasi sitemap...');

        try {
            Cache::forget('sitemap.xml');
            $sitemapUrl = url('/sitemap.xml');
            $this->info('Memuat sitemap dari: ' . $sitemapUrl);

            // Ambil konten sitemap untuk me-regenerasi cache
            file_get_contents($sitemapUrl);

            $this->info('✓ Sitemap berhasil diregenerasi');
            Log::info('Sitemap berhasil diregenerasi via command');
        } catch (\Exception $e) {
            $this->error('✗ Gagal meregenerasi sitemap: ' . $e->getMessage());
            Log::error('Gagal meregenerasi sitemap: ' . $e->getMessage());
        }
    }
}