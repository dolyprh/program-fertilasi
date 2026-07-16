<?php

use App\Http\Controllers\ClassificationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\ModelInformationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return redirect()->route('login');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::resource('classifications', ClassificationController::class)
        ->only(['index', 'create', 'store', 'show']);

    Route::get('/model-information', ModelInformationController::class)
        ->name('model-information');

    Route::prefix('master-data')->name('master-data.')->group(function () {
        Route::redirect('/', '/master-data/doctors')->name('index');
        Route::get('/{type}', [MasterDataController::class, 'manage'])->name('manage');
        Route::post('/{type}', [MasterDataController::class, 'store'])->name('store');
        Route::put('/{type}/{id}', [MasterDataController::class, 'update'])->name('update');
        Route::delete('/{type}/{id}', [MasterDataController::class, 'destroy'])->name('destroy');
    });
});

require __DIR__.'/settings.php';
