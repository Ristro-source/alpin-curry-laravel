<?php

use App\Http\Controllers\LegacyMenuController;
use App\Http\Controllers\MenuDataApiController;
use App\Http\Controllers\MenuPreviewController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ReservationController;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/en'));

Route::middleware(['web', 'auth'])->prefix('admin')->group(function (): void {
    Route::get('/menu-preview', [MenuPreviewController::class, 'html'])->name('admin.menu-preview');
    Route::get('/menu-download', [MenuPreviewController::class, 'pdf'])->name('admin.menu-download');
    Route::get('/legacy-food-menu', [LegacyMenuController::class, 'food'])->name('admin.legacy-food-menu');
    Route::get('/legacy-drinks-menu', [LegacyMenuController::class, 'drinks'])->name('admin.legacy-drinks-menu');
    Route::get('/menu-data', MenuDataApiController::class)->name('admin.menu-data');
});

Route::pattern('locale', 'en|it|de');

Route::prefix('{locale}')
    ->middleware(SetLocale::class)
    ->group(function (): void {
        Route::get('/', [PageController::class, 'home'])->name('home');
        Route::get('/menu', [PageController::class, 'menu'])->name('menu');
        Route::get('/gallery', [PageController::class, 'gallery'])->name('gallery');
        Route::get('/faq', [PageController::class, 'faq'])->name('faq');
        Route::get('/legal', [PageController::class, 'legal'])->name('legal');
        Route::get('/legal/privacy', [PageController::class, 'privacy'])->name('legal.privacy');
        Route::get('/legal/cookies', [PageController::class, 'cookies'])->name('legal.cookies');
        Route::get('/legal/impressum', [PageController::class, 'impressum'])->name('legal.impressum');
        Route::get('/legal/terms', [PageController::class, 'terms'])->name('legal.terms');

        Route::post('/reservations', [ReservationController::class, 'store'])
            ->middleware('throttle:reservation-submissions')
            ->name('reservations.store');
        Route::post('/contact', [ContactController::class, 'store'])
            ->middleware('throttle:contact-submissions')
            ->name('contact.store');
    });
