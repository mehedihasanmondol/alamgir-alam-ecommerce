<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Models\Media;
use App\Models\ImageUploadSetting;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Exception;

class ImageService
{
    /**
     * Get the maximum upload size from PHP configuration in bytes
     */
    public static function getMaxUploadSize(): int
    {
        $uploadMax = self::parseSize(ini_get('upload_max_filesize'));
        $postMax = self::parseSize(ini_get('post_max_size'));
        $memoryLimit = self::parseSize(ini_get('memory_limit'));

        // Return the smallest of the three
        return min($uploadMax, $postMax, $memoryLimit);
    }

    /**
     * Get the maximum upload size in human-readable format
     */
    public static function getMaxUploadSizeFormatted(): string
    {
        return self::formatBytes(self::getMaxUploadSize());
    }

    /**
     * Parse size string from php.ini to bytes
     */
    private static function parseSize(string $size): int
    {
        $size = trim($size);
        $unit = strtolower($size[strlen($size) - 1]);
        $value = (int) $size;

        switch ($unit) {
            case 'g':
                $value *= 1024 * 1024 * 1024;
                break;
            case 'm':
                $value *= 1024 * 1024;
                break;
            case 'k':
                $value *= 1024;
                break;
        }

        return $value;
    }

    /**
     * Format bytes to human-readable size
     */
    private static function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Validate file size against PHP ini limits
     */
    public static function validateFileSize(UploadedFile $file): bool
    {
        $maxSize = self::getMaxUploadSize();
        return $file->getSize() <= $maxSize;
    }

    /**
     * Process and convert image to WebP format with compression
     * 
     * @param UploadedFile $file
     * @param string $folder Folder name in storage/app/public
     * @param int $quality Quality percentage (1-100), default 85
     * @return string Path to the stored image
     * @throws Exception
     */
    public static function processAndStore(UploadedFile $file, string $folder = 'products', int $quality = 85): string
    {
        // Validate file size
        if (!self::validateFileSize($file)) {
            throw new Exception('File size exceeds the maximum allowed size of ' . self::getMaxUploadSizeFormatted());
        }

        // Validate it's an image
        if (!in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/bmp'])) {
            throw new Exception('File must be an image (JPEG, PNG, GIF, WebP, or BMP)');
        }

        try {
            // Generate unique filename
            $filename = uniqid() . '_' . time() . '.webp';
            $path = $folder . '/' . $filename;
            $fullPath = storage_path('app/public/' . $path);

            // Create directory if it doesn't exist
            $directory = dirname($fullPath);
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            // Load and process image using Intervention Image v3
            $manager = new ImageManager(new Driver());
            $image = $manager->read($file->getRealPath());
            
            // Encode to WebP with compression
            // Quality: 85 is a good balance between size and quality
            // For more aggressive compression, use 75-80
            $encoded = $image->toWebp($quality);

            // Save the image
            $encoded->save($fullPath);

            return $path;
        } catch (Exception $e) {
            throw new Exception('Failed to process image: ' . $e->getMessage());
        }
    }

    /**
     * Process and convert image to WebP with aggressive compression
     * Uses lower quality for maximum size reduction
     * 
     * @param UploadedFile $file
     * @param string $folder
     * @return string
     */
    public static function processAndStoreCompressed(UploadedFile $file, string $folder = 'products'): string
    {
        return self::processAndStore($file, $folder, 75); // More aggressive compression
    }

    /**
     * Process and convert image to WebP with high quality
     * Uses higher quality for better image appearance
     * 
     * @param UploadedFile $file
     * @param string $folder
     * @return string
     */
    public static function processAndStoreHighQuality(UploadedFile $file, string $folder = 'products'): string
    {
        return self::processAndStore($file, $folder, 90); // Higher quality
    }

    /**
     * Delete image file from storage
     */
    public static function delete(string $path): bool
    {
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }
        return false;
    }

    /**
     * Get image dimensions without loading entire image
     */
    public static function getImageDimensions(string $path): ?array
    {
        $fullPath = storage_path('app/public/' . $path);
        
        if (!file_exists($fullPath)) {
            return null;
        }

        $size = getimagesize($fullPath);
        
        if ($size === false) {
            return null;
        }

        return [
            'width' => $size[0],
            'height' => $size[1],
            'mime' => $size['mime'] ?? null,
        ];
    }

    /**
     * Create thumbnail from WebP image
     * 
     * @param string $sourcePath Path to source image in storage
     * @param int $width Thumbnail width
     * @param int $height Thumbnail height
     * @param string $folder Folder to store thumbnail
     * @return string Path to thumbnail
     */
    public static function createThumbnail(string $sourcePath, int $width = 300, int $height = 300, string $folder = 'products/thumbnails'): string
    {
        $fullSourcePath = storage_path('app/public/' . $sourcePath);
        
        if (!file_exists($fullSourcePath)) {
            throw new Exception('Source image not found');
        }

        // Generate thumbnail filename
        $filename = 'thumb_' . uniqid() . '_' . time() . '.webp';
        $path = $folder . '/' . $filename;
        $fullPath = storage_path('app/public/' . $path);

        // Create directory if it doesn't exist
        $directory = dirname($fullPath);
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        // Create thumbnail using Intervention Image v3
        $manager = new ImageManager(new Driver());
        $image = $manager->read($fullSourcePath);
        
        // Cover fit to dimensions (crop to fit)
        $image->cover($width, $height);
        
        // Encode to WebP and save
        $encoded = $image->toWebp(85);
        $encoded->save($fullPath);

        return $path;
    }

    /**
     * Batch process multiple images
     * 
     * @param array $files Array of UploadedFile objects
     * @param string $folder
     * @param int $quality
     * @return array Array of paths
     */
    public static function processBatch(array $files, string $folder = 'products', int $quality = 85): array
    {
        $paths = [];
        $errors = [];

        foreach ($files as $index => $file) {
            try {
                $paths[] = self::processAndStore($file, $folder, $quality);
            } catch (Exception $e) {
                $errors[$index] = $e->getMessage();
            }
        }

        if (!empty($errors)) {
            throw new Exception('Some images failed to process: ' . json_encode($errors));
        }

        return $paths;
    }

    /**
     * Process uploaded image with WebP conversion, multiple sizes, and save to Media library
     * 
     * @param UploadedFile|string $file File or base64 blob
     * @param array $options Configuration options
     * @return Media
     */
    public static function processUniversalUpload($file, array $options = []): Media
    {
        $settings = self::getUploadSettings($options);
        
        // Handle base64 blob from cropper
        if (is_string($file) && str_starts_with($file, 'data:image')) {
            $file = self::base64ToUploadedFile($file);
        }
        
        // Validate
        self::validateUpload($file, $settings);
        
        // Generate paths
        $year = date('Y');
        $month = date('m');
        $folder = "images/{$year}/{$month}";
        $filename = self::generateFilename($file, 'webp');
        
        // Process original/large
        $largePath = self::processImageSize($file, $folder, $filename, 'l', $settings);
        
        // Process medium
        $mediumPath = self::processImageSize($file, $folder, $filename, 'm', $settings);
        
        // Process small
        $smallPath = self::processImageSize($file, $folder, $filename, 's', $settings);
        
        // Get original image info
        $manager = new ImageManager(new Driver());
        $image = $manager->read($file instanceof UploadedFile ? $file->getRealPath() : $file);
        $width = $image->width();
        $height = $image->height();
        
        // Store in database
        $media = Media::create([
            'user_id' => auth()->id(),
            'original_filename' => $file instanceof UploadedFile ? $file->getClientOriginalName() : 'cropped_image.webp',
            'filename' => $filename,
            'mime_type' => 'image/webp',
            'extension' => 'webp',
            'size' => $file instanceof UploadedFile ? $file->getSize() : strlen($file),
            'width' => $width,
            'height' => $height,
            'aspect_ratio' => $width / $height,
            'disk' => $settings['disk'],
            'path' => $largePath,
            'large_path' => $largePath,
            'medium_path' => $mediumPath,
            'small_path' => $smallPath,
            'scope' => $settings['scope'] ?? 'global',
            'metadata' => [
                'compression' => $settings['compression'],
                'created_by' => auth()->user()->name ?? 'Unknown',
            ],
        ]);
        
        // Run optimizer if enabled
        if ($settings['enable_optimizer']) {
            self::optimizeImage($largePath, $settings['disk']);
            self::optimizeImage($mediumPath, $settings['disk']);
            self::optimizeImage($smallPath, $settings['disk']);
        }
        
        return $media;
    }
    
    /**
     * Process image to specific size variant
     */
    private static function processImageSize($file, string $folder, string $filename, string $prefix, array $settings): string
    {
        $sizeConfig = match($prefix) {
            'l' => ['width' => $settings['size_large_width'], 'height' => $settings['size_large_height']],
            'm' => ['width' => $settings['size_medium_width'], 'height' => $settings['size_medium_height']],
            's' => ['width' => $settings['size_small_width'], 'height' => $settings['size_small_height']],
        };
        
        $prefixedFilename = "{$prefix}__{$filename}";
        $path = $folder . '/' . $prefixedFilename;
        $fullPath = Storage::disk($settings['disk'])->path($path);
        
        // Create directory
        $directory = dirname($fullPath);
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
        
        // Load and resize
        $manager = new ImageManager(new Driver());
        $image = $manager->read($file instanceof UploadedFile ? $file->getRealPath() : $file);
        
        // Scale down maintaining aspect ratio
        $image->scale(
            width: $sizeConfig['width'],
            height: $sizeConfig['height']
        );
        
        // Encode to WebP
        $encoded = $image->toWebp($settings['compression']);
        $encoded->save($fullPath);
        
        return $path;
    }
    
    /**
     * Convert base64 blob to temporary file
     */
    private static function base64ToUploadedFile(string $base64): UploadedFile
    {
        // Extract base64 data
        preg_match('/^data:image\/(\w+);base64,/', $base64, $matches);
        $extension = $matches[1] ?? 'png';
        $data = substr($base64, strpos($base64, ',') + 1);
        $data = base64_decode($data);
        
        // Create temporary file
        $tmpFile = tempnam(sys_get_temp_dir(), 'upload_');
        file_put_contents($tmpFile, $data);
        
        return new UploadedFile(
            $tmpFile,
            'cropped_' . time() . '.' . $extension,
            'image/' . $extension,
            null,
            true
        );
    }
    
    /**
     * Get merged upload settings
     */
    private static function getUploadSettings(array $options): array
    {
        return array_merge([
            'disk' => ImageUploadSetting::get('storage_disk', 'public'),
            'compression' => ImageUploadSetting::get('default_compression', 70),
            'size_large_width' => ImageUploadSetting::get('size_large_width', 1920),
            'size_large_height' => ImageUploadSetting::get('size_large_height', 1920),
            'size_medium_width' => ImageUploadSetting::get('size_medium_width', 1200),
            'size_medium_height' => ImageUploadSetting::get('size_medium_height', 1200),
            'size_small_width' => ImageUploadSetting::get('size_small_width', 600),
            'size_small_height' => ImageUploadSetting::get('size_small_height', 600),
            'max_file_size' => ImageUploadSetting::get('max_file_size', 5) * 1024 * 1024, // Convert MB to bytes
            'max_width' => ImageUploadSetting::get('max_width', 4000),
            'max_height' => ImageUploadSetting::get('max_height', 4000),
            'enable_optimizer' => ImageUploadSetting::get('enable_optimizer', true),
            'scope' => ImageUploadSetting::get('default_library_scope', 'global'),
        ], $options);
    }
    
    /**
     * Validate upload against settings
     */
    private static function validateUpload($file, array $settings): void
    {
        if ($file instanceof UploadedFile) {
            // Check file size
            if ($file->getSize() > $settings['max_file_size']) {
                throw new Exception('File size exceeds maximum allowed: ' . self::formatBytes($settings['max_file_size']));
            }
            
            // Check MIME type
            $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/bmp'];
            if (!in_array($file->getMimeType(), $allowedMimes)) {
                throw new Exception('Invalid file type. Allowed: JPEG, PNG, GIF, WebP, BMP');
            }
        }
        
        // Check dimensions
        $manager = new ImageManager(new Driver());
        $image = $manager->read($file instanceof UploadedFile ? $file->getRealPath() : $file);
        
        if ($image->width() > $settings['max_width'] || $image->height() > $settings['max_height']) {
            throw new Exception("Image dimensions exceed maximum: {$settings['max_width']}x{$settings['max_height']}px");
        }
    }
    
    /**
     * Generate unique filename
     */
    private static function generateFilename($file, string $extension): string
    {
        $originalName = $file instanceof UploadedFile 
            ? pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)
            : 'image';
        
        $slug = Str::slug($originalName);
        $unique = uniqid() . '_' . time();
        
        return "{$slug}_{$unique}.{$extension}";
    }
    
    /**
     * Run Spatie optimizer on image
     */
    private static function optimizeImage(string $path, string $disk): void
    {
        try {
            $fullPath = Storage::disk($disk)->path($path);
            
            if (file_exists($fullPath)) {
                $optimizerChain = OptimizerChainFactory::create();
                $optimizerChain->optimize($fullPath);
            }
        } catch (Exception $e) {
            // Silently fail optimizer - image is already processed
            logger()->warning("Image optimization failed: {$e->getMessage()}");
        }
    }
    
    /**
     * Delete media and all size variants
     */
    public static function deleteMedia(Media $media): bool
    {
        return $media->deleteWithFiles();
    }
}
