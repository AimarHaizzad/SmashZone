<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class CloudinaryService
{
    private $cloudinary;

    public function __construct()
    {
        // Support CLOUDINARY_URL format (cloudinary://api_key:api_secret@cloud_name)
        $cloudinaryUrl = env('CLOUDINARY_URL');
        
        if ($cloudinaryUrl) {
            // Parse CLOUDINARY_URL format
            Configuration::instance([
                'url' => $cloudinaryUrl
            ]);
        } else {
            // Fallback to individual environment variables
            Configuration::instance([
                'cloud' => [
                    'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                    'api_key' => env('CLOUDINARY_API_KEY'),
                    'api_secret' => env('CLOUDINARY_API_SECRET'),
                ],
                'url' => [
                    'secure' => true
                ]
            ]);
        }

        $this->cloudinary = new Cloudinary();
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
            // Check if Cloudinary is configured (either via URL or individual vars)
            $cloudinaryUrl = env('CLOUDINARY_URL');
            $hasIndividualCreds = env('CLOUDINARY_CLOUD_NAME') && env('CLOUDINARY_API_KEY') && env('CLOUDINARY_API_SECRET');
            
            if (!$cloudinaryUrl && !$hasIndividualCreds) {
                Log::error('Cloudinary credentials not configured');
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

            return [
                'public_id' => $result['public_id'],
                'secure_url' => $result['secure_url'],
                'url' => $result['url'],
            ];
        } catch (\Exception $e) {
            Log::error('Cloudinary upload failed', [
                'error' => $e->getMessage(),
                'folder' => $folder,
                'file' => $file->getClientOriginalName()
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
            // Check if Cloudinary is configured (either via URL or individual vars)
            $cloudinaryUrl = env('CLOUDINARY_URL');
            $hasIndividualCreds = env('CLOUDINARY_CLOUD_NAME') && env('CLOUDINARY_API_KEY') && env('CLOUDINARY_API_SECRET');
            
            if (!$cloudinaryUrl && !$hasIndividualCreds) {
                Log::error('Cloudinary credentials not configured');
                return false;
            }

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

