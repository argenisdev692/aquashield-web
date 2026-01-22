<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image; // Usar el Facade de Intervention para Laravel 11+
use Illuminate\Support\Str;
use Carbon\Carbon;

class PortfolioImageService
{
    private const MAX_IMAGE_DIMENSION = 1200;
    private const IMAGE_QUALITY = 80;
    private const THUMB_DIMENSION = 300;
    
    /**
     * Store and optimize portfolio image in S3
     *
     * @param UploadedFile|string $image
     * @return string|null URL of the stored image
     */
    public function storeImage($image): ?string
    {
        try {
            if (!$image) {
                return null;
            }
            
            // Create directory structure based on date: portfolios/2023/05/25/
            $now = Carbon::now();
            $storagePath = 'portfolios/' . $now->year . '/' . $now->format('m') . '/' . $now->format('d');
            
            Log::info('Storing portfolio image', [
                'storage_path' => $storagePath
            ]);
            
            // Store original and optimized image
            $imageUrl = $this->processAndStoreImage($image, $storagePath);
            
            return $imageUrl;
        } catch (\Exception $e) {
            Log::error('Error storing portfolio image', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }
    
    /**
     * Process and store image in S3 with optimizations
     *
     * @param UploadedFile|string $image
     * @param string $storagePath
     * @return string URL of the stored image
     */
    private function processAndStoreImage($image, string $storagePath): string
    {
        // Create optimized version of the image
        $optimizedImagePath = $this->optimizeImage($image);
        
        // Generate thumbnail
        $thumbnailPath = $this->createThumbnail($optimizedImagePath);
        
        // Generate unique filenames
        $uniqueId = Str::uuid()->toString();
        $mainFilename = $uniqueId . '.jpg';
        $thumbnailFilename = $uniqueId . '-thumb.jpg';
        
        // Store in S3
        $mainImageS3Path = $storagePath . '/' . $mainFilename;
        $thumbnailS3Path = $storagePath . '/thumbnails/' . $thumbnailFilename;
        
        // Upload to S3
        Storage::disk('s3')->put($mainImageS3Path, file_get_contents($optimizedImagePath));
        Storage::disk('s3')->put($thumbnailS3Path, file_get_contents($thumbnailPath));
        
        // Clean up temporary files
        if (file_exists($optimizedImagePath)) {
            unlink($optimizedImagePath);
        }
        if (file_exists($thumbnailPath)) {
            unlink($thumbnailPath);
        }
        
        // Return the main image URL
        return Storage::disk('s3')->url($mainImageS3Path);
    }
    
    /**
     * Optimize image for web
     *
     * @param UploadedFile|string $image
     * @return string Path to optimized image
     */
    private function optimizeImage($image): string
    {
        $img = Image::make($image);
        $originalWidth = $img->width();
        $originalHeight = $img->height();
        
        // Resize if larger than max dimensions while maintaining aspect ratio
        if ($originalWidth > self::MAX_IMAGE_DIMENSION || $originalHeight > self::MAX_IMAGE_DIMENSION) {
            $img->resize(self::MAX_IMAGE_DIMENSION, self::MAX_IMAGE_DIMENSION, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }
        
        // Create temp file path
        $tempPath = sys_get_temp_dir() . '/' . uniqid('portfolio_') . '.jpg';
        
        // Save with specified quality
        $img->save($tempPath, self::IMAGE_QUALITY);
        
        return $tempPath;
    }
    
    /**
     * Create thumbnail version of image
     *
     * @param string $imagePath
     * @return string Path to thumbnail
     */
    private function createThumbnail(string $imagePath): string
    {
        $img = Image::make($imagePath);
        
        // Create a squared thumbnail by centering and cropping
        $img->fit(self::THUMB_DIMENSION, self::THUMB_DIMENSION);
        
        // Create temp file path for thumbnail
        $tempPath = sys_get_temp_dir() . '/' . uniqid('portfolio_thumb_') . '.jpg';
        
        // Save with specified quality
        $img->save($tempPath, self::IMAGE_QUALITY);
        
        return $tempPath;
    }
    
    /**
     * Delete image from S3
     *
     * @param string $imageUrl URL of the image to delete
     * @return bool
     */
    public function deleteImage(?string $imageUrl): bool
    {
        if (empty($imageUrl)) {
            return false;
        }
        
        try {
            // Extract the path from URL
            $path = $this->getRelativePathFromUrl($imageUrl);
            
            // Also try to find and delete the thumbnail
            $pathInfo = pathinfo($path);
            $thumbnailPath = $pathInfo['dirname'] . '/thumbnails/' . $pathInfo['filename'] . '-thumb.' . $pathInfo['extension'];
            
            // Delete main image
            $mainDeleted = Storage::disk('s3')->exists($path) ? Storage::disk('s3')->delete($path) : false;
            
            // Try to delete thumbnail (but don't fail if not found)
            $thumbDeleted = Storage::disk('s3')->exists($thumbnailPath) ? Storage::disk('s3')->delete($thumbnailPath) : true;
            
            Log::info('Deleted portfolio image', [
                'main_image' => $path,
                'thumb_image' => $thumbnailPath,
                'main_deleted' => $mainDeleted,
                'thumb_deleted' => $thumbDeleted
            ]);
            
            return $mainDeleted;
        } catch (\Exception $e) {
            Log::error('Error deleting portfolio image', [
                'url' => $imageUrl,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Extract relative path from S3 URL
     *
     * @param string $url
     * @return string
     */
    private function getRelativePathFromUrl(string $url): string
    {
        // Remove any query parameters
        $url = strtok($url, '?');
        
        // Parse the URL
        $parsedUrl = parse_url($url);
        
        // Get the path component
        $path = $parsedUrl['path'] ?? '';
        
        // Remove leading slash and bucket name if present
        $path = ltrim($path, '/');
        $bucketName = env('AWS_BUCKET');
        $path = preg_replace("/^{$bucketName}\//", '', $path);
        
        return $path;
    }

    /**
     * Get thumbnail URL from main image URL
     *
     * @param string $imageUrl
     * @return string|null
     */
    public function getThumbnailUrl(string $imageUrl): ?string
    {
        try {
            $path = $this->getRelativePathFromUrl($imageUrl);
            $pathInfo = pathinfo($path);
            $thumbnailPath = $pathInfo['dirname'] . '/thumbnails/' . $pathInfo['filename'] . '-thumb.' . $pathInfo['extension'];
            
            return Storage::disk('s3')->url($thumbnailPath);
        } catch (\Exception $e) {
            Log::error('Error generating thumbnail URL', [
                'url' => $imageUrl,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Batch delete multiple images
     *
     * @param array $imageUrls
     * @return array Results of deletion attempts
     */
    public function deleteMultipleImages(array $imageUrls): array
    {
        $results = [];
        
        foreach ($imageUrls as $url) {
            $results[$url] = $this->deleteImage($url);
        }
        
        Log::info('Batch deleted portfolio images', [
            'total_images' => count($imageUrls),
            'successful_deletions' => count(array_filter($results)),
            'failed_deletions' => count($imageUrls) - count(array_filter($results))
        ]);
        
        return $results;
    }

    /**
     * Store multiple images in batch
     *
     * @param array $images Array of UploadedFile objects
     * @return array Array of stored image URLs
     */
    public function storeMultipleImages(array $images): array
    {
        $storedUrls = [];
        
        foreach ($images as $index => $image) {
            $url = $this->storeImage($image);
            if ($url) {
                $storedUrls[] = $url;
            } else {
                Log::warning('Failed to store image in batch', ['index' => $index]);
            }
        }
        
        Log::info('Batch stored portfolio images', [
            'total_attempted' => count($images),
            'successful_uploads' => count($storedUrls),
            'failed_uploads' => count($images) - count($storedUrls)
        ]);
        
        return $storedUrls;
    }
}