<?php

use App\Http\Controllers\UploadController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::post("/upload", [UploadController::class, 'store']);

Route::get('/test-broadcast', function () {
    broadcast(new \App\Events\ImageTransformed('test/2JcqF5te1XM0lOkQC01SZ43ZLLH9TKqDNGf0RB4i.jpg', '2JcqF5te1XM0lOkQC01SZ43ZLLH9TKqDNGf0RB4i.jpg'));
    return 'Event broadcasted';
});
