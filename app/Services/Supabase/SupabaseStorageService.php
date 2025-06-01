<?php

namespace App\Services\Supabase;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SupabaseStorageService
{
    /**
     * @var string Supabase project URL
     */
    protected $supabaseUrl;

    /**
     * @var string Supabase API key
     */
    protected $supabaseKey;

    /**
     * @var string Bucket name
     */
    protected $bucket;

    /**
     * @var Client HTTP Client
     */
    protected $httpClient;

    /**
     * SupabaseStorageService constructor.
     */
    public function __construct()
    {
        $this->supabaseUrl = env('SUPABASE_URL');
        $this->supabaseKey = env('SUPABASE_KEY');
        $this->bucket = env('SUPABASE_BUCKET', 'smartphones');

        $this->httpClient = new Client([
            'base_uri' => $this->supabaseUrl . '/storage/v1/',
            'headers' => [
                'Authorization' => 'Bearer ' . $this->supabaseKey,
                'apikey' => $this->supabaseKey,
            ],
        ]);
    }

    /**
     * Upload file to Supabase storage.
     *
     * @param string $filePath Path to file to upload
     * @param string $fileName Custom filename for storage (optional)
     * @return string|null Public URL of uploaded file or null on failure
     */
    public function uploadFile(string $filePath, string $fileName = null): ?string
    {
        try {
            if (!File::exists($filePath)) {
                Log::error("File not found at path: {$filePath}");
                return null;
            }

            // Generate a unique filename if not provided
            if (!$fileName) {
                $extension = File::extension($filePath);
                $fileName = Str::slug(pathinfo($filePath, PATHINFO_FILENAME)) . '-' . time() . '.' . $extension;
            }

            // Create file stream for upload
            $fileStream = fopen($filePath, 'r');

            // Upload file to Supabase
            $response = $this->httpClient->request('POST', "object/{$this->bucket}/{$fileName}", [
                'headers' => [
                    'Content-Type' => File::mimeType($filePath),
                    'x-upsert' => 'true', // Enable upsert to replace existing files with the same name
                ],
                'body' => $fileStream
            ]);

            if ($response->getStatusCode() !== 200) {
                Log::error("Failed to upload file to Supabase. Status code: {$response->getStatusCode()}");
                return null;
            }

            // Generate the public URL
            $publicUrl = $this->supabaseUrl . '/storage/v1/object/public/' . $this->bucket . '/' . $fileName;

            Log::info("File uploaded successfully to Supabase. URL: {$publicUrl}");
            return $publicUrl;
        } catch (Exception $e) {
            Log::error("Error uploading file to Supabase: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Upload a file from an UploadedFile instance.
     *
     * @param UploadedFile $file The uploaded file
     * @param string $customName Custom filename (optional)
     * @return string|null Public URL of uploaded file or null on failure
     */
    public function uploadFromRequest(UploadedFile $file, string $customName = null): ?string
    {
        try {
            // Use original name if custom name not provided
            $fileName = $customName ?: Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '-' . time() . '.' . $file->getClientOriginalExtension();

            return $this->uploadFile($file->getPathname(), $fileName);
        } catch (Exception $e) {
            Log::error("Error uploading file from request to Supabase: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Delete a file from Supabase storage.
     *
     * @param string $fileUrl Full public URL of the file
     * @return bool Whether the deletion was successful
     */
    public function deleteFile(string $fileUrl): bool
    {
        try {
            // Extract the filename from URL
            $path = parse_url($fileUrl, PHP_URL_PATH);
            if (!$path) {
                return false;
            }

            $pathParts = explode('/', $path);
            $fileName = end($pathParts);

            // Delete from Supabase
            $response = $this->httpClient->request('DELETE', "object/{$this->bucket}/{$fileName}");

            $success = $response->getStatusCode() === 200;
            if ($success) {
                Log::info("File deleted successfully from Supabase: {$fileName}");
            } else {
                Log::warning("Failed to delete file from Supabase. Status code: {$response->getStatusCode()}");
            }

            return $success;
        } catch (Exception $e) {
            Log::error("Error deleting file from Supabase: {$e->getMessage()}");
            return false;
        }
    }
}