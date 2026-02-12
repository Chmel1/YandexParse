<?php

use App\Http\Controllers\ReviewController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/reviews', [ReviewController::class, 'index']);
Route::post('/settings', [ReviewController::class, 'storeSettings']);