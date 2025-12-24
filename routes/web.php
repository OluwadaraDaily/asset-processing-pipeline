<?php

use App\Http\Controllers\UploadController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('ImageResize');
})->name('home');

Route::post('/upload', [UploadController::class, 'store']);
