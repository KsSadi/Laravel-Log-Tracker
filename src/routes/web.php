<?php

use Illuminate\Support\Facades\Route;

use Kssadi\LogTracker\Http\Controllers\LogTrackerController;


Route::group([
    'prefix' => config('log-tracker.route_prefix', 'log-tracker'),
    'middleware' => config('log-tracker.middleware', ['web', 'auth']),
    'as' => 'log-tracker.',
], function () {
    Route::get('/', [LogTrackerController::class, 'dashboard'])->name('dashboard');
    Route::get('/log-file', [LogTrackerController::class, 'index'])->name('index');
    Route::get('/download/{logName}', [LogTrackerController::class, 'download'])->name('download');
    Route::post('/delete/{logName}', [LogTrackerController::class, 'delete'])->name('delete');
    Route::post('/clear/{logName}', [LogTrackerController::class, 'clear'])->name('clear');

    //  EXPORT ROUTES
    Route::get('/export', [LogTrackerController::class, 'exportForm'])->name('export.form');
    Route::post('/export', [LogTrackerController::class, 'export'])->name('export');
    Route::get('/export/{logName}/{format}', [LogTrackerController::class, 'quickExport'])->name('export.quick');

    Route::get('/{logName}', [LogTrackerController::class, 'show'])->name('show');

    Route::get('/api/dashboard-refresh', [LogTrackerController::class, 'dashboardRefresh'])->name('api.dashboard.refresh');

});

