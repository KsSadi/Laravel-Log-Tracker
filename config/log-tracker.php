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
    | Log Levels Configuration
    |--------------------------------------------------------------------------
    |
    | Defines color codes and icons for different log levels.
    | This helps in visually distinguishing logs.
    | Each log level has:
    | - 'color': A hexadecimal color code.
    | - 'icon': A FontAwesome icon class.
    |
    */
    'log_levels' => [
        'emergency' => [
            'color' => '#DC143C', // Dark Red
            'icon'  => 'fas fa-skull-crossbones' // Skull icon (Critical emergency)
        ],
        'alert' => [
            'color' => '#FF0000', // Bright Red
            'icon'  => 'fas fa-bell' // Bell icon (Alert notification)
        ],
        'critical' => [
            'color' => '#FF4500', // Orange Red
            'icon'  => 'fas fa-exclamation-triangle' // Warning triangle icon
        ],
        'error' => [
            'color' => '#FF6347', // Tomato Red
            'icon'  => 'fas fa-exclamation-circle' // Exclamation circle icon (Error)
        ],
        'warning' => [
            'color' => '#FFA500', // Orange
            'icon'  => 'fas fa-exclamation-triangle' // Warning triangle icon
        ],
        'notice' => [
            'color' => '#32CD32', // Lime Green
            'icon'  => 'fas fa-info-circle' // Info circle icon (Less severe notice)
        ],
        'info' => [
            'color' => '#1E90FF', // Dodger Blue
            'icon'  => 'fas fa-info-circle' // Info circle icon (General information)
        ],
        'debug' => [
            'color' => '#696969', // Gray
            'icon'  => 'fas fa-bug' // Bug icon (Debugging information)
        ],
        'total' => [
            'color' => '#008000', // Green
            'icon'  => 'fas fa-file-alt' // Document icon (Total logs count)
        ],
    ],

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
    'allow_delete' => false,

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

];
