<?php

namespace App\Console\Commands;

use App\Models\Smartphone;
use App\Services\SmartphoneImageService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MigrateSmartphoneImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'smartphones:migrate-images {--force : Force migration without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate smartphone images from local storage to Supabase';

    /**
     * The smartphone image service.
     *
     * @var SmartphoneImageService
     */
    protected $smartphoneImageService;

    /**
     * Create a new command instance.
     *
     * @param SmartphoneImageService $smartphoneImageService
     * @return void
     */
    public function __construct(SmartphoneImageService $smartphoneImageService)
    {
        parent::__construct();
        $this->smartphoneImageService = $smartphoneImageService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting migration of smartphone images to Supabase...');

        // Ensure Supabase is configured
        if (!env('SUPABASE_URL') || !env('SUPABASE_KEY')) {
            $this->error('Supabase not configured! Please set SUPABASE_URL and SUPABASE_KEY in your .env file.');
            return 1;
        }

        // Get all smartphones with local images
        $smartphones = Smartphone::whereRaw("image_url NOT LIKE '%supabase%'")
            ->whereRaw("image_url != 'images/no-image.png'")
            ->get();

        $count = $smartphones->count();

        if ($count === 0) {
            $this->info('No local images found that need migration.');
            return 0;
        }

        $this->info("Found {$count} smartphone(s) with local images to migrate.");

        // Confirm migration
        if (!$this->option('force') && !$this->confirm('Do you want to proceed with migration?')) {
            $this->info('Migration aborted.');
            return 0;
        }

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $success = 0;
        $failed = 0;

        foreach ($smartphones as $smartphone) {
            try {
                $localPath = public_path($smartphone->getRawOriginal('image_url'));

                if (!File::exists($localPath)) {
                    $this->newLine();
                    $this->warn("File not found for smartphone ID {$smartphone->id}: {$localPath}");
                    $failed++;
                    $bar->advance();
                    continue;
                }

                // Create a UploadedFile instance from the local file for processing
                $tempFile = tempnam(sys_get_temp_dir(), 'migrate_');
                copy($localPath, $tempFile);

                $uploadedFile = new \Illuminate\Http\UploadedFile(
                    $tempFile,
                    basename($localPath),
                    File::mimeType($localPath),
                    null,
                    true
                );

                // Upload to Supabase
                $imageUrl = $this->smartphoneImageService->processAndUpload($uploadedFile, $smartphone->name);

                // Clean up temporary file
                if (file_exists($tempFile)) {
                    unlink($tempFile);
                }

                if ($imageUrl) {
                    // Update image_url in database
                    $smartphone->update(['image_url' => $imageUrl]);

                    // Success
                    $success++;
                    Log::info("Migrated image for smartphone ID {$smartphone->id} to Supabase: {$imageUrl}");
                } else {
                    $this->newLine();
                    $this->warn("Failed to upload image for smartphone ID {$smartphone->id} to Supabase");
                    $failed++;
                }
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("Error migrating image for smartphone ID {$smartphone->id}: {$e->getMessage()}");
                Log::error("Error migrating image: {$e->getMessage()}");
                $failed++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Migration completed: {$success} successful, {$failed} failed.");

        if ($this->confirm('Do you want to delete local images that were successfully migrated?')) {
            $deleted = 0;

            $smartphones = Smartphone::whereRaw("image_url LIKE '%supabase%'")->get();

            foreach ($smartphones as $smartphone) {
                // Check if the old image path is stored somewhere (could be in a backup field)
                // For this example, we'll just check all files in the images/smartphones directory
                $directory = public_path('images/smartphones');

                if (File::isDirectory($directory)) {
                    $files = File::files($directory);

                    foreach ($files as $file) {
                        if (Str::contains($file->getFilename(), Str::slug($smartphone->name))) {
                            File::delete($file->getPathname());
                            $deleted++;
                        }
                    }
                }
            }

            $this->info("Deleted {$deleted} local images.");
        }

        return 0;
    }
}