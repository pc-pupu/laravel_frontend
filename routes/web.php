<?php

use App\Http\Controllers\Web\AdminController;
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
Route::prefix('admin')
    ->middleware(\App\Http\Middleware\CheckSessionAuth::class)
    ->group(function () {

    // Page Views
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/users', [AdminController::class, 'usersPage'])->name('admin.users');
    Route::get('/roles', [AdminController::class, 'rolesPage'])->name('admin.roles');
    Route::get('/permissions', [AdminController::class, 'permissionsPage'])->name('admin.permissions');
    Route::get('/error-logs', [AdminController::class, 'errorLogsPage'])->name('admin.error-logs');

    // USERS PROXY
    Route::get('/users/list', [AdminController::class, 'listUsers']);
    Route::get('/users/{id}', [AdminController::class, 'getUser']);
    Route::post('/users', [AdminController::class, 'createUser']);
    Route::put('/users/{id}', [AdminController::class, 'updateUser']);
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser']);

    // ROLES PROXY
    Route::get('/roles/list', [AdminController::class, 'listRoles']);
    Route::get('/roles/{id}', [AdminController::class, 'getRole']);
    Route::post('/roles', [AdminController::class, 'createRole']);
    Route::put('/roles/{id}', [AdminController::class, 'updateRole']);
    Route::delete('/roles/{id}', [AdminController::class, 'deleteRole']);

    // PERMISSIONS
    Route::get('/permissions/list', [AdminController::class, 'listPermissions']);
    Route::get('/permissions/{id}', [AdminController::class, 'getPermission']);
    Route::post('/permissions', [AdminController::class, 'createPermission']);
    Route::put('/permissions/{id}', [AdminController::class, 'updatePermission']);
    Route::delete('/permissions/{id}', [AdminController::class, 'deletePermission']);

    // ERROR LOGS
    Route::get('/error-logs/list', [AdminController::class, 'listErrorLogs']);
    Route::get('/error-logs/{id}', [AdminController::class, 'getErrorLog']);
    Route::delete('/error-logs/{id}', [AdminController::class, 'deleteErrorLog']);
    Route::delete('/error-logs', [AdminController::class, 'clearAllErrorLogs']);
});



/* Frontend URL End */
