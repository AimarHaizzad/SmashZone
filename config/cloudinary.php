<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cloudinary Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for Cloudinary integration.
    | You can get your Cloudinary URL from your Cloudinary dashboard.
    |
    | Format: cloudinary://API_KEY:API_SECRET@CLOUD_NAME
    |
    */

    'cloud_url' => env('CLOUDINARY_URL'),

    /*
    |--------------------------------------------------------------------------
    | Cloudinary Individual Credentials (Alternative)
    |--------------------------------------------------------------------------
    |
    | If you prefer to use individual credentials instead of CLOUDINARY_URL,
    | you can set these values. Note: CLOUDINARY_URL takes precedence.
    |
    */

    'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
    'api_key' => env('CLOUDINARY_API_KEY'),
    'api_secret' => env('CLOUDINARY_API_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Secure URLs
    |--------------------------------------------------------------------------
    |
    | Whether to use secure (HTTPS) URLs for Cloudinary images.
    |
    */

    'secure' => env('CLOUDINARY_SECURE', true),

];
