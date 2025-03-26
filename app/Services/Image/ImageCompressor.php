<?php

namespace App\Services\Image;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Interfaces\EncodedImageInterface;

class ImageCompressor
{
    /**
     * Maximum file size in KB before compression is applied
     * 
     * @var int
     */
    protected $maxFileSize = 250;

    /**
     * Maximum width of compressed images
     * 
     * @var int
     */
    protected $maxWidth = 1200;

    /**
     * @var ImageManager
     */
    protected $imageManager;

    /**
     * ImageCompressor constructor.
     */
    public function __construct()
    {
        $this->imageManager = new ImageManager(new Driver());
    }

    /**
     * Compress an image if needed, based on file size
     * Always maintain PNG format but reduce size if needed
     * 
     * @param UploadedFile|string $file The uploaded file or path to local file
     * @return string Path to the compressed file (or original if no compression applied)
     */
    public function compressIfNeeded($file): string
    {
        // Determine if we're working with an uploaded file or a file path
        $isUploadedFile = $file instanceof UploadedFile;
        $filePath = $isUploadedFile ? $file->getPathname() : $file;
        $fileSize = $isUploadedFile ? $file->getSize() / 1024 : filesize($filePath) / 1024; // Convert to KB
        $extension = $isUploadedFile ? $file->getClientOriginalExtension() : pathinfo($filePath, PATHINFO_EXTENSION);

        // If file is small enough, no need to compress
        if ($fileSize <= $this->maxFileSize) {
            return $filePath;
        }

        try {
            // Create a temporary file for the compressed output (always PNG)
            $tempPath = sys_get_temp_dir() . '/' . uniqid('compressed_') . '.png';

            // Try different approaches for compression
            $currentSize = $fileSize;
            $success = false;

            for ($attempts = 0; $attempts < 3 && $currentSize > $this->maxFileSize; $attempts++) {
                // Calculate target dimensions based on attempt number
                $targetWidth = $this->maxWidth;

                if ($attempts > 0) {
                    // Reduce dimensions progressively with each attempt
                    $scaleFactor = 1 - ($attempts * 0.2);  // 80%, 60% of original max width
                    $targetWidth = intval($this->maxWidth * $scaleFactor);
                }

                // Read the image fresh
                $img = $this->imageManager->read($filePath);

                // Resize if image is larger than target width
                if ($img->width() > $targetWidth) {
                    $img->scale(width: $targetWidth);
                }

                // Save as PNG (without any compression parameters)
                $encoded = $img->toPng();

                // Write to temporary file
                file_put_contents($tempPath, $encoded->toString());

                // Check new size
                $currentSize = filesize($tempPath) / 1024;
                if ($currentSize <= $this->maxFileSize) {
                    $success = true;
                    break;
                }
            }

            // If we couldn't get it under the target size, use the last result anyway
            // Log the compression details
            $reduction = round((($fileSize - $currentSize) / $fileSize) * 100, 1);

            if ($success) {
                Log::info("PNG image compressed successfully: {$fileSize}KB → {$currentSize}KB ({$reduction}% reduction)");
            } else {
                Log::warning("PNG image compressed but still above target size: {$fileSize}KB → {$currentSize}KB ({$reduction}% reduction)");
            }

            return $tempPath;
        } catch (\Exception $e) {
            Log::error("Image compression failed: " . $e->getMessage());
            // Return original file if compression fails
            return $filePath;
        }
    }
}