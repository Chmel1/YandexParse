<?php

use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth; 
use Inertia\Inertia;

// Корень сайта
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('settings.edit');
    }
    return redirect()->route('login');
});


require __DIR__.'/auth.php';


Route::middleware(['auth', 'verified'])->group(function () {
    
    
    Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');

    
    Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
    
});