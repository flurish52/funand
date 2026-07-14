<?php

use App\Http\Controllers\AskController;
use App\Http\Controllers\AskResponseController;
use App\Http\Controllers\PublicAskController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn () => Inertia::render('Home'))->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [AskController::class, 'index'])->name('dashboard');
});

// Dashboard (auth-only) — manage your own asks
Route::middleware(['auth'])->group(function () {
    Route::resource('asks', AskController::class)->except(['show']);
    Route::patch('asks/{ask}/toggle-publish', [AskController::class, 'togglePublish'])
        ->name('asks.toggle-publish');

//    Route::get('/responses', [AskResponseController::class, 'index'])->name('asks.responses.index');

});

// Public — no auth, this is the shareable link people receive
Route::get('/for-{slug}', [PublicAskController::class, 'show'])->name('ask.public.show');
Route::post('/for-{slug}/respond', [PublicAskController::class, 'respond'])->name('ask.public.respond');


require __DIR__.'/settings.php';
