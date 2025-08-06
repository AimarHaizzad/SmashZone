<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Excel export settings
    |--------------------------------------------------------------------------
    |
    | Here you can configure the settings for Excel exports.
    |
    */

    'exports' => [

        /*
        |--------------------------------------------------------------------------
        | Chunk size
        |--------------------------------------------------------------------------
        |
        | When using FromQuery, the query is automatically chunked.
        | Here you can specify how big the chunk should be.
        |
        */
        'chunk_size' => 1000,

        /*
        |--------------------------------------------------------------------------
        | Temporary files
        |--------------------------------------------------------------------------
        |
        | Export jobs are processed using temporary files. Here you can configure
        | the temporary file settings.
        |
        */
        'temp_files' => [

            /*
            |--------------------------------------------------------------------------
            | Local temporary path
            |--------------------------------------------------------------------------
            |
            | When using local temporary files, this is where they will be stored.
            |
            */
            'local_path' => storage_path('app/laravel-excel'),

        ],

        /*
        |--------------------------------------------------------------------------
        | CSV Settings
        |--------------------------------------------------------------------------
        |
        | Configure the settings for CSV exports.
        |
        */
        'csv' => [

            /*
            |--------------------------------------------------------------------------
            | Delimiter
            |--------------------------------------------------------------------------
            |
            | The delimiter used in the CSV file.
            |
            */
            'delimiter' => ',',

            /*
            |--------------------------------------------------------------------------
            | Enclosure
            |--------------------------------------------------------------------------
            |
            | The enclosure used in the CSV file.
            |
            */
            'enclosure' => '"',

            /*
            |--------------------------------------------------------------------------
            | Escape character
            |--------------------------------------------------------------------------
            |
            | The escape character used in the CSV file.
            |
            */
            'escape_character' => '\\',

            /*
            |--------------------------------------------------------------------------
            | Contiguous
            |--------------------------------------------------------------------------
            |
            | Whether to write the CSV file contiguously.
            |
            */
            'contiguous' => false,

            /*
            |--------------------------------------------------------------------------
            | Input encoding
            |--------------------------------------------------------------------------
            |
            | The input encoding used when reading the CSV file.
            |
            */
            'input_encoding' => 'UTF-8',

        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Value Binder
    |--------------------------------------------------------------------------
    |
    | PhpSpreadsheet offers a way to hook into the process of a value being
    | written to a cell. In there some assumptions are made on how the
    | value should be formatted. If you want to change those defaults,
    | you can implement your own default value binder.
    |
    | Possible value binders:
    |
    | [x] Maatwebsite\Excel\DefaultValueBinder::class
    | [x] Maatwebsite\Excel\StringValueBinder::class
    | [x] Maatwebsite\Excel\BooleanValueBinder::class
    | [x] Maatwebsite\Excel\NullValueBinder::class
    |
    */
    'value_binder' => [
        'default' => \Maatwebsite\Excel\DefaultValueBinder::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    |
    | By default PhpSpreadsheet keeps all cell values in memory, however when
    | dealing with large files, this can result into memory issues. If you
    | want to mitigate that, you can configure a cache driver here.
    | When using the illuminate driver, it will store each value in the
    | cache store. This can slow down the process, because it needs to
    | store each value. You can use the "batch" store if you want to
    | only persist to the store when the memory limit is reached.
    |
    | Default: illuminate
    | Supported: memory|illuminate|batch
    |
    */
    'cache' => [
        'driver' => 'memory',
        'batch' => [
            'memory_limit' => 60000,
        ],
        'illuminate' => [
            'store' => null,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Transaction handler
    |--------------------------------------------------------------------------
    |
    | By default the import is wrapped in a transaction. This is useful
    | for when an import may fail and you want to retry it. With the
    | transactions, the previous import gets rolled-back and can be
    | retried. Specify the class responsible for handling this.
    |
    | Supported handlers: null|db
    |
    */
    'transactions' => [
        'handler' => 'db',
    ],

]; 