<?php

namespace App\Services;

use App\Services\Image\ImageCompressor;
use App\Services\Supabase\SupabaseStorageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SmartphoneImageService
{
    /**
     * @var ImageCompressor
     */
    protected $imageCompressor;

    /**
     * @var SupabaseStorageService
     */
    protected $supabaseStorage;

    /**
     * SmartphoneImageService constructor.
     *
     * @param ImageCompressor $imageCompressor
     * @param SupabaseStorageService $supabaseStorage
     */
    public function __construct(
        ImageCompressor $imageCompressor,
        SupabaseStorageService $supabaseStorage
    ) {
        $this->imageCompressor = $imageCompressor;
        $this->supabaseStorage = $supabaseStorage;
    }

    /**
     * Process and upload a smartphone image.
     *
     * @param UploadedFile $file
     * @param string $smartphoneName
     * @return string|null URL of uploaded image or null on failure
     */
    public function processAndUpload(UploadedFile $file, string $smartphoneName): ?string
    {
        try {
            // Validasi format file harus PNG
            $fileExtension = strtolower($file->getClientOriginalExtension());
            if ($fileExtension !== 'png') {
                Log::error('Format file tidak valid: ' . $fileExtension . '. Hanya file PNG yang diizinkan.');
                return null;
            }

            // Compress image if above max size
            $compressedImagePath = $this->imageCompressor->compressIfNeeded($file);

            // Create custom filename
            $fileName = Str::slug($smartphoneName) . '-' . time() . '.png';

            // Upload to Supabase
            $imageUrl = $this->supabaseStorage->uploadFile($compressedImagePath, $fileName);

            // Clean up temporary file if it's not the original
            if ($compressedImagePath !== $file->getPathname() && file_exists($compressedImagePath)) {
                unlink($compressedImagePath);
            }

            return $imageUrl;
        } catch (\Exception $e) {
            Log::error('Error processing and uploading smartphone image: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Delete a smartphone image from Supabase.
     *
     * @param string $imageUrl
     * @return bool
     */
    public function deleteImage(string $imageUrl): bool
    {
        try {
            return $this->supabaseStorage->deleteFile($imageUrl);
        } catch (\Exception $e) {
            Log::error('Error deleting smartphone image: ' . $e->getMessage());
            return false;
        }
    }
}