<?php

use App\Http\Controllers\Api\ImageStatusController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\UploadController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Auth Routes (guest only)
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

// Logout (auth required)
Route::post('/logout', [LogoutController::class, 'destroy'])->name('logout')->middleware('auth');

// Landing page
Route::get('/', function () {
    return Inertia::render('Landing');
})->name('landing');

// Image Resizer App (no auth required - guests can use with session)
Route::get('/image-resizer', function () {
    return Inertia::render('ImageResize');
})->name('app.image-resize');

Route::get('/test', function () {
    return Inertia::render('TestingGround');
})->name('testing-ground');

Route::post('/upload', [UploadController::class, 'store']);

// API Routes
Route::prefix('api')->group(function () {
    Route::get('/images/{uuid}/status', [ImageStatusController::class, 'show'])->name('api.images.status');
});
