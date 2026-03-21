<?php

use Illuminate\Support\Facades\Route;
use Kssadi\LogTracker\Http\Controllers\CompareController;
use Kssadi\LogTracker\Http\Controllers\DashboardController;
use Kssadi\LogTracker\Http\Controllers\ExportController;
use Kssadi\LogTracker\Http\Controllers\LogFileController;
use Kssadi\LogTracker\Http\Controllers\SearchController;

Route::group([
    'prefix' => config('log-tracker.route_prefix', 'log-tracker'),
    'middleware' => config('log-tracker.middleware', ['web', 'auth']),
    'as' => 'log-tracker.',
], function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/api/dashboard-refresh', [DashboardController::class, 'refresh'])->name('api.dashboard.refresh');

    // Log file management
    Route::get('/log-file', [LogFileController::class, 'index'])->name('index');
    Route::get('/download/{logName}', [LogFileController::class, 'download'])->name('download');
    Route::post('/delete/{logName}', [LogFileController::class, 'delete'])->name('delete');
    Route::post('/clear/{logName}', [LogFileController::class, 'clear'])->name('clear');

    // Search (must be before /{logName} wildcard)
    Route::get('/search', [SearchController::class, 'index'])->name('search');

    // Compare (must be before /{logName} wildcard)
    Route::get('/compare', [CompareController::class, 'index'])->name('compare');

    // Export (must be before /{logName} wildcard)
    Route::get('/export', [ExportController::class, 'form'])->name('export.form');
    Route::post('/export', [ExportController::class, 'export'])->name('export');
    Route::get('/export/{logName}/{format}', [ExportController::class, 'quickExport'])->name('export.quick');

    // Wildcard — must remain last, constrained to .log files to block non-log lookups
    Route::get('/{logName}', [LogFileController::class, 'show'])->name('show')->where('logName', '.+\.log');
});
