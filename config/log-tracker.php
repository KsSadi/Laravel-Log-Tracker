<?php
/**
 * Log Tracker Configuration File
 *
 * This file contains configuration settings for the log tracker package.
 *
 * @author  Md. Khaled Saifullah Sadi
 * @link    https://github.com/KsSadi/Laravel-Log-Tracker
 * @license MIT
 */


return [

    /*
    |--------------------------------------------------------------------------
    | Route Prefix
    |--------------------------------------------------------------------------
    |
    | This defines the base route for accessing the log tracker.
    | Example: If set to 'log-tracker', logs can be accessed via:
    |          https://yourdomain.com/log-tracker
    |
    */
    'route_prefix' => 'log-tracker',

    /*
    |--------------------------------------------------------------------------
    | Middleware
    |--------------------------------------------------------------------------
    |
    | Middleware that should be applied to the log tracker routes.
    | Default:
    | - 'web': Ensures session and cookie-based authentication.
    | - 'auth': Restricts access to authenticated users only.
    |
    */
    'middleware' => ['web', 'auth'],

    /*
    |--------------------------------------------------------------------------
    | Theme Selection
    |--------------------------------------------------------------------------
    |
    | Choose the theme for the log viewer UI.
    | Available options:
    | - 'LiteFlow' : Minimal, clean log view.
    | - 'GlowStack': Modern, colorful, enhanced visual log view.
    |
    | Default: 'GlowStack'
    |
    */

    'theme' => 'GlowStack',

    /*
    |--------------------------------------------------------------------------
    | Pagination Settings
    |--------------------------------------------------------------------------
    |
    | 'per_page' controls how many log entries are displayed per page.
    | Default: 50 entries per page.
    |
    */
    'per_page' => 50,

    /*
    |--------------------------------------------------------------------------
    | Maximum Log File Size (MB)
    |--------------------------------------------------------------------------
    |
    | Defines the maximum file size (in MB) that can be processed.
    | Default: 50 MB.
    |
    */
    'max_file_size' => 50,

    /*
    |--------------------------------------------------------------------------
    | Log File Deletion Permission
    |--------------------------------------------------------------------------
    |
    | If set to true, users can delete log files via the UI.
    | Default: false (Disables log deletion).
    |
    */
    'allow_delete' => true,

    /*
    |--------------------------------------------------------------------------
    | Log File Download Permission
    |--------------------------------------------------------------------------
    |
    | If set to true, users can download log files.
    | Default: true (Allows downloading logs).
    |
    */
    'allow_download' => true,

    /*
   |--------------------------------------------------------------------------
   | Export Configuration (No Dependencies Required)
   |--------------------------------------------------------------------------
   */
    'export' => [
        'enabled' => true,
        'formats' => [
            'csv' => [
                'enabled' => true,
                'description' => 'Excel-compatible CSV format'
            ],
            'json' => [
                'enabled' => true,
                'description' => 'Structured JSON with metadata'
            ],
            'excel' => [
                'enabled' => true,
                'description' => 'Native Excel XML format'
            ],
            'pdf' => [
                'enabled' => true,
                'description' => 'Print-ready HTML report'
            ],
        ],
        'limits' => [
            'max_entries' => 50000,
            'max_file_size_mb' => 50,
            'timeout_seconds' => 300,
        ],
        'storage' => [
            'cleanup_after_days' => 7,
        ],
    ],

];
