<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class CloudinaryService
{
    private $cloudinary;
    private $isConfigured = false;

    public function __construct()
    {
        // Don't initialize Cloudinary in constructor - do it lazily when needed
        $this->isConfigured = $this->checkConfiguration();
    }

    /**
     * Check if Cloudinary is configured
     */
    private function checkConfiguration(): bool
    {
        // Use config() instead of env() for better Laravel practices
        $cloudinaryUrl = config('cloudinary.cloud_url');
        $hasIndividualCreds = config('cloudinary.cloud_name') && 
                              config('cloudinary.api_key') && 
                              config('cloudinary.api_secret');
        
        return !empty($cloudinaryUrl) || $hasIndividualCreds;
    }

    /**
     * Initialize Cloudinary connection
     */
    private function initializeCloudinary(): void
    {
        if ($this->cloudinary !== null) {
            return; // Already initialized
        }

        if (!$this->isConfigured) {
            Log::warning('Cloudinary is not configured. Image uploads will be skipped.');
            return; // Don't throw exception, just return
        }

        try {
            // Use config() instead of env() for better Laravel practices
            // Support CLOUDINARY_URL format (cloudinary://api_key:api_secret@cloud_name)
            $cloudinaryUrl = config('cloudinary.cloud_url');
            
            if ($cloudinaryUrl) {
                // Fix common URL format issues - ensure @ symbol is present
                if (strpos($cloudinaryUrl, '@') === false && preg_match('/cloudinary:\/\/(\d+):([^@]+)([a-z0-9]+)/', $cloudinaryUrl, $matches)) {
                    // Fix missing @ symbol: cloudinary://key:secretcloudname -> cloudinary://key:secret@cloudname
                    $cloudinaryUrl = 'cloudinary://' . $matches[1] . ':' . $matches[2] . '@' . $matches[3];
                    Log::warning('Fixed Cloudinary URL format - added missing @ symbol');
                }
                
                // Parse CLOUDINARY_URL format
                Configuration::instance([
                    'url' => $cloudinaryUrl
                ]);
            } else {
                // Fallback to individual configuration values
                $cloudName = config('cloudinary.cloud_name');
                $apiKey = config('cloudinary.api_key');
                $apiSecret = config('cloudinary.api_secret');
                
                if (!$cloudName || !$apiKey || !$apiSecret) {
                    Log::warning('Cloudinary individual credentials are incomplete');
                    return;
                }
                
                Configuration::instance([
                    'cloud' => [
                        'cloud_name' => $cloudName,
                        'api_key' => $apiKey,
                        'api_secret' => $apiSecret,
                    ],
                    'url' => [
                        'secure' => config('cloudinary.secure', true)
                    ]
                ]);
            }

            $this->cloudinary = new Cloudinary();
        } catch (\Throwable $e) {
            Log::error('Failed to initialize Cloudinary', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            // Don't throw exception - let uploadImage handle it by returning null
            $this->isConfigured = false;
        }
    }

    /**
     * Upload an image to Cloudinary
     *
     * @param UploadedFile $file
     * @param string $folder Folder name in Cloudinary (e.g., 'products', 'courts')
     * @param string|null $publicId Optional public ID for the image
     * @return array|null Returns array with 'public_id' and 'secure_url', or null on failure
     */
    public function uploadImage(UploadedFile $file, string $folder, ?string $publicId = null): ?array
    {
        try {
            // Check if Cloudinary is configured
            if (!$this->isConfigured) {
                Log::warning('Cloudinary credentials not configured - skipping upload');
                return null;
            }

            // Initialize Cloudinary if not already done
            $this->initializeCloudinary();
            
            // Check again after initialization (in case initialization failed)
            if ($this->cloudinary === null) {
                Log::warning('Cloudinary initialization failed - skipping upload');
                return null;
            }

            $uploadOptions = [
                'folder' => $folder,
                'resource_type' => 'image',
                'overwrite' => true,
            ];

            if ($publicId) {
                $uploadOptions['public_id'] = $publicId;
            }

            $result = $this->cloudinary->uploadApi()->upload(
                $file->getRealPath(),
                $uploadOptions
            );

            if (!isset($result['public_id']) || !isset($result['secure_url'])) {
                Log::error('Cloudinary upload returned invalid response', [
                    'result' => $result
                ]);
                return null;
            }

            return [
                'public_id' => $result['public_id'],
                'secure_url' => $result['secure_url'],
                'url' => $result['url'] ?? $result['secure_url'],
            ];
        } catch (\Throwable $e) {
            Log::error('Cloudinary upload failed', [
                'error' => $e->getMessage(),
                'folder' => $folder,
                'file' => $file->getClientOriginalName(),
                'trace' => $e->getTraceAsString(),
                'file_path' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return null;
        }
    }

    /**
     * Delete an image from Cloudinary
     *
     * @param string $publicId The public ID of the image to delete
     * @return bool
     */
    public function deleteImage(string $publicId): bool
    {
        try {
            // Check if Cloudinary is configured
            if (!$this->isConfigured) {
                Log::error('Cloudinary credentials not configured');
                return false;
            }

            // Initialize Cloudinary if not already done
            $this->initializeCloudinary();

            $result = $this->cloudinary->uploadApi()->destroy($publicId);
            return isset($result['result']) && $result['result'] === 'ok';
        } catch (\Exception $e) {
            Log::error('Cloudinary delete failed', [
                'error' => $e->getMessage(),
                'public_id' => $publicId
            ]);
            return false;
        }
    }

    /**
     * Get the secure URL for an image
     *
     * @param string $publicId The public ID of the image
     * @return string|null
     */
    public function getImageUrl(string $publicId): ?string
    {
        try {
            if (!$publicId) {
                return null;
            }

            // If it's already a full URL, return it
            if (filter_var($publicId, FILTER_VALIDATE_URL)) {
                return $publicId;
            }

            // Check if Cloudinary is configured
            if (!$this->isConfigured) {
                return null;
            }

            // Initialize Cloudinary if not already done
            $this->initializeCloudinary();

            // Generate Cloudinary URL
            return $this->cloudinary->image($publicId)->secure()->toUrl();
        } catch (\Exception $e) {
            Log::error('Failed to get Cloudinary image URL', [
                'error' => $e->getMessage(),
                'public_id' => $publicId
            ]);
            return null;
        }
    }

    /**
     * Extract public ID from Cloudinary URL or return as is if it's already a public ID
     *
     * @param string $urlOrPublicId
     * @return string
     */
    public function extractPublicId(string $urlOrPublicId): string
    {
        // If it's already a public ID (no http/https), return as is
        if (!filter_var($urlOrPublicId, FILTER_VALIDATE_URL)) {
            return $urlOrPublicId;
        }

        // Extract public ID from Cloudinary URL
        // Cloudinary URLs format: https://res.cloudinary.com/{cloud_name}/image/upload/{version}/{public_id}.{format}
        $pattern = '/\/upload\/(?:v\d+\/)?(.+?)(?:\.[^.]+)?$/';
        if (preg_match($pattern, $urlOrPublicId, $matches)) {
            return $matches[1];
        }

        return $urlOrPublicId;
    }
}

