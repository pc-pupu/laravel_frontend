<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\FrontendController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\LoginController;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/show-data', [FrontendController::class, 'showData']);

/* Frontend URL Start */
Route::controller(HomeController::class)->group(function () {
    Route::get('/', 'homePage')->name('homepage');
    Route::get('home', 'index')->name('index');
});

Route::controller(FrontendController::class)->group(function () {
    // Additional frontend routes can be added here
    Route::get('/about-us', 'about_us')->name('about-us');
    Route::get('/contact-us', 'contact_us')->name('contact-us');
    Route::get('/faq', 'faq')->name('faq');
    Route::get('/notice', 'notice')->name('notice');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('captcha/{config?}', '\Mews\Captcha\CaptchaController@getCaptcha');

Route::get('dashboard', function () {
    return view('outerTheme.pages.dashboard');
})->name('dashboard')->middleware(\App\Http\Middleware\CheckSessionAuth::class);

// Admin routes
Route::prefix('admin')->middleware(\App\Http\Middleware\CheckSessionAuth::class)->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Web\AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/users', [\App\Http\Controllers\Web\AdminController::class, 'users'])->name('admin.users');
    Route::get('/roles', [\App\Http\Controllers\Web\AdminController::class, 'roles'])->name('admin.roles');
    Route::get('/permissions', [\App\Http\Controllers\Web\AdminController::class, 'permissions'])->name('admin.permissions');
    Route::get('/error-logs', [\App\Http\Controllers\Web\AdminController::class, 'errorLogs'])->name('admin.error-logs');
});

/* Frontend URL End */
