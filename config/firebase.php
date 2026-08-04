<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Firebase Cloud Messaging
    |--------------------------------------------------------------------------
    |
    | Provide credentials via FIREBASE_CREDENTIALS_PATH (recommended locally)
    | or FIREBASE_CREDENTIALS / FIREBASE_CREDENTIALS_BASE64 for hosted envs.
    | Never commit the service account JSON to version control.
    |
    */

    'project_id' => env('FIREBASE_PROJECT_ID'),

    'credentials_path' => env('FIREBASE_CREDENTIALS_PATH'),

    'credentials' => env('FIREBASE_CREDENTIALS'),

    'credentials_base64' => env('FIREBASE_CREDENTIALS_BASE64'),

];
