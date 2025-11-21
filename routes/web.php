<?php

use App\Http\Controllers\Web\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\FrontendController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\LoginController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\CmsContentManagerController;
use App\Http\Controllers\Web\ExistingApplicantController;
use App\Http\Controllers\Web\ExistingOccupantController;
use App\Http\Controllers\Web\ExistingApplicantVsCsController;

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

Route::get('dashboard', DashboardController::class)
    ->name('dashboard')
    ->middleware(\App\Http\Middleware\CheckSessionAuth::class);

Route::prefix('cms-content')
    ->middleware(\App\Http\Middleware\CheckSessionAuth::class)
    ->name('cms-content.')
    ->group(function () {
        Route::get('/', [CmsContentManagerController::class, 'index'])->name('index');
        Route::get('/create', [CmsContentManagerController::class, 'create'])->name('create');
        Route::post('/', [CmsContentManagerController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [CmsContentManagerController::class, 'edit'])->name('edit');
        Route::put('/{id}', [CmsContentManagerController::class, 'update'])->name('update');
        Route::delete('/{id}', [CmsContentManagerController::class, 'destroy'])->name('destroy');
    });

// Existing Applicant (Legacy/Physical Applicants)
Route::prefix('existing-applicant')
    ->middleware(\App\Http\Middleware\CheckSessionAuth::class)
    ->name('existing-applicant.')
    ->group(function () {
        Route::get('/', [ExistingApplicantController::class, 'index'])->name('index');
        Route::get('/with-hrms', [ExistingApplicantController::class, 'withHrms'])->name('with-hrms');
        Route::get('/without-hrms', [ExistingApplicantController::class, 'withoutHrms'])->name('without-hrms');
        Route::get('/search', [ExistingApplicantController::class, 'search'])->name('search');
        Route::post('/search', [ExistingApplicantController::class, 'searchSubmit'])->name('search.submit');
        Route::get('/create', [ExistingApplicantController::class, 'create'])->name('create');
        Route::post('/store', [ExistingApplicantController::class, 'store'])->name('store');
        Route::get('/{id}/view', [ExistingApplicantController::class, 'view'])->name('view');
        Route::get('/{id}/edit', [ExistingApplicantController::class, 'edit'])->name('edit');
        Route::put('/{id}/update', [ExistingApplicantController::class, 'update'])->name('update');
        Route::post('/{appId}/accept-declaration/{hrmsId}/{uid}', [ExistingApplicantController::class, 'acceptDeclaration'])->name('accept-declaration');
    });

// Existing Occupant
Route::prefix('existing-occupant')
    ->middleware(\App\Http\Middleware\CheckSessionAuth::class)
    ->name('existing-occupant.')
    ->group(function () {
        Route::get('/', [ExistingOccupantController::class, 'index'])->name('index');
        Route::get('/with-hrms', [ExistingOccupantController::class, 'withHrms'])->name('with-hrms');
        Route::get('/without-hrms', [ExistingOccupantController::class, 'withoutHrms'])->name('without-hrms');
        Route::get('/flat-list', [ExistingOccupantController::class, 'flatList'])->name('flat-list');
        Route::get('/create', [ExistingOccupantController::class, 'create'])->name('create');
        Route::get('/create/{flat_id}', [ExistingOccupantController::class, 'create'])->name('create.flat');
        Route::post('/', [ExistingOccupantController::class, 'store'])->name('store');
        Route::get('/{id}/view', [ExistingOccupantController::class, 'view'])->name('view');
        Route::get('/{id}/edit', [ExistingOccupantController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ExistingOccupantController::class, 'update'])->name('update');
        Route::delete('/{id}', [ExistingOccupantController::class, 'destroy'])->name('destroy');
    });

// Existing Applicant VS/CS (Floor Shifting / Category Shifting)
Route::prefix('existing-applicant-vs-cs')
    ->middleware(\App\Http\Middleware\CheckSessionAuth::class)
    ->name('existing-applicant-vs-cs.')
    ->group(function () {
        Route::get('/flat-wise-form', [ExistingApplicantVsCsController::class, 'flatWiseForm'])->name('flat-wise-form');
        Route::get('/flat-details', [ExistingApplicantVsCsController::class, 'getFlatDetails'])->name('flat-details');
        Route::get('/create/{uid}', [ExistingApplicantVsCsController::class, 'create'])->name('create');
        Route::post('/', [ExistingApplicantVsCsController::class, 'store'])->name('store');
        Route::get('/vs-list-with-hrms', [ExistingApplicantVsCsController::class, 'vsListWithHrms'])->name('vs-list-with-hrms');
        Route::get('/vs-list-without-hrms', [ExistingApplicantVsCsController::class, 'vsListWithoutHrms'])->name('vs-list-without-hrms');
        Route::get('/cs-list-with-hrms', [ExistingApplicantVsCsController::class, 'csListWithHrms'])->name('cs-list-with-hrms');
        Route::get('/cs-list-without-hrms', [ExistingApplicantVsCsController::class, 'csListWithoutHrms'])->name('cs-list-without-hrms');
        Route::get('/{id}/edit', [ExistingApplicantVsCsController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ExistingApplicantVsCsController::class, 'update'])->name('update');
    });

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
    Route::get('/sidebar-menus', [AdminController::class, 'sidebarMenusPage'])->name('admin.sidebar-menus');

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

    // SIDEBAR MENUS
    Route::get('/sidebar-menus/list', [AdminController::class, 'listSidebarMenus']);
    Route::get('/sidebar-menus/{id}', [AdminController::class, 'getSidebarMenu']);
    Route::post('/sidebar-menus', [AdminController::class, 'createSidebarMenu']);
    Route::put('/sidebar-menus/{id}', [AdminController::class, 'updateSidebarMenu']);
    Route::delete('/sidebar-menus/{id}', [AdminController::class, 'deleteSidebarMenu']);
});



/* Frontend URL End */
