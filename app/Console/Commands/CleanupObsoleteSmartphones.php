<?php

namespace App\Console\Commands;

use App\Http\Controllers\SmartphoneController;
use Illuminate\Console\Command;

class CleanupObsoleteSmartphones extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'smartphones:cleanup-obsolete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hapus smartphone dengan tahun rilis lebih dari 2 tahun dari database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $controller = app(SmartphoneController::class);
        $result = $controller->cleanupObsoleteSmartphones();

        $this->info($result);
        return 0;
    }
}