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
use App\Http\Controllers\Web\EstateTreasuryMappingController;
use App\Http\Controllers\Web\UserSsoController;
use App\Http\Controllers\Web\SsoDashboardController;
use App\Http\Controllers\Web\UserTaggingController;

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

// User SSO Login Routes (matching Drupal routes)
Route::get('/user/sso/{token}', [UserSsoController::class, 'hrmsSsoLogin'])->where('token', '.*')->name('user-sso.hrms-sso');
Route::get('/sso/ddo/{token}', [UserSsoController::class, 'ddoSsoLogin'])->where('token', '.*')->name('user-sso.ddo-sso');
Route::get('/hrms-login', [UserSsoController::class, 'hrmsLoginForm'])->name('user-sso.hrms-login');
Route::post('/hrms-login', [UserSsoController::class, 'hrmsLoginSubmit'])->name('user-sso.hrms-login-submit');
Route::get('/get-key', [UserSsoController::class, 'getApiKey'])->name('user-sso.get-key');
Route::get('/get-test-info/{hrmsId?}', [UserSsoController::class, 'getTestInfo'])->name('user-sso.get-test-info');

Route::get('captcha/{config?}', '\Mews\Captcha\CaptchaController@getCaptcha');

// Unified Dashboard (role-based content)
Route::get('dashboard', DashboardController::class)
    ->name('dashboard')
    ->middleware(\App\Http\Middleware\CheckSessionAuth::class);

// User Tagging Routes (matching Drupal URLs)
Route::middleware(\App\Http\Middleware\CheckSessionAuth::class)->group(function () {
    Route::get('user-tagging', [UserTaggingController::class, 'create'])->name('user-tagging.create');
    Route::post('user-tagging', [UserTaggingController::class, 'store'])->name('user-tagging.store');
    Route::get('flat-wise-user-info', [UserTaggingController::class, 'flatWiseUserInfo'])->name('user-tagging.flat-wise-user-info');
    Route::get('flat-wise-user-info-details/{flat_id}', [UserTaggingController::class, 'flatWiseUserDetails'])->name('user-tagging.flat-wise-user-details');
    Route::post('flat-wise-user-info-details/{flat_id}', [UserTaggingController::class, 'updateStatus'])->name('user-tagging.update-status');
    Route::get('tagged-user-list/{status?}', [UserTaggingController::class, 'taggedUserList'])->name('user-tagging.tagged-user-list');
});

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

// Existing Applicant (Legacy/Physical Applicants) - Matching Drupal URLs
Route::middleware(\App\Http\Middleware\CheckSessionAuth::class)->group(function () {
    // In Drupal, existing_applicant_entry is the form itself (create form)
    Route::get('existing_applicant_entry', [ExistingApplicantController::class, 'create'])->name('existing-applicant.create');
    Route::post('existing_applicant_entry', [ExistingApplicantController::class, 'store'])->name('existing-applicant.store');
    Route::get('existing-applicant-list', [ExistingApplicantController::class, 'index'])->name('existing-applicant.index');
    Route::get('view-legacy-applicant-list-whrms', [ExistingApplicantController::class, 'withHrms'])->name('existing-applicant.with-hrms');
    Route::get('view-legacy-applicant-list-wohrms', [ExistingApplicantController::class, 'withoutHrms'])->name('existing-applicant.without-hrms');
    Route::get('search-with-physical-application-no', [ExistingApplicantController::class, 'search'])->name('existing-applicant.search');
    Route::post('search-with-physical-application-no', [ExistingApplicantController::class, 'searchSubmit'])->name('existing-applicant.search.submit');
    Route::get('physical-application-view/{id}', [ExistingApplicantController::class, 'view'])->name('existing-applicant.view');
    Route::get('physical-application-edit/{id}', [ExistingApplicantController::class, 'edit'])->name('existing-applicant.edit');
    Route::post('physical-application-edit/{id}', [ExistingApplicantController::class, 'update'])->name('existing-applicant.update');
    Route::put('physical-application-edit/{id}', [ExistingApplicantController::class, 'update'])->name('existing-applicant.update.put');
    Route::post('physical-application-accept-declaration/{appId}/{hrmsId}/{uid}', [ExistingApplicantController::class, 'acceptDeclaration'])->name('existing-applicant.accept-declaration');
});

// Existing Occupant - Matching Drupal URLs
Route::middleware(\App\Http\Middleware\CheckSessionAuth::class)->group(function () {
    Route::get('rhewise_flatlist', [ExistingOccupantController::class, 'index'])->name('existing-occupant.index');
    Route::get('rhewise_flatlist_draft', [ExistingOccupantController::class, 'indexDraft'])->name('existing-occupant.index-draft');
    Route::get('rhewise_occupant_data_entry/{flat_id}', [ExistingOccupantController::class, 'create'])->name('existing-occupant.create');
    Route::get('rhewise_occupant_draft_data_entry/{flat_id}', [ExistingOccupantController::class, 'createDraft'])->name('existing-occupant.create-draft');
    Route::post('rhewise_occupant_data_entry/{flat_id}', [ExistingOccupantController::class, 'store'])->name('existing-occupant.store');
    Route::post('rhewise_occupant_draft_data_entry/{flat_id}', [ExistingOccupantController::class, 'storeDraft'])->name('existing-occupant.store-draft');
    Route::get('rhewise_occupant_draft_list', [ExistingOccupantController::class, 'withoutHrms'])->name('existing-occupant.without-hrms');
    Route::get('existing-occupant-list-wohrms', [ExistingOccupantController::class, 'withoutHrms'])->name('existing-occupant.without-hrms-alt');
    Route::get('existing-occupant-list-whrms', [ExistingOccupantController::class, 'withHrms'])->name('existing-occupant.with-hrms');
    Route::get('view-occupant-list', [ExistingOccupantController::class, 'withHrms'])->name('existing-occupant.with-hrms-alt');
    Route::get('existing-occupant-view-det/{id}', [ExistingOccupantController::class, 'view'])->name('existing-occupant.view');
    Route::get('existing-occupant-view-det-draft/{id}', [ExistingOccupantController::class, 'viewDraft'])->name('existing-occupant.view-draft');
    Route::get('existing-occupant-edit/{id}', [ExistingOccupantController::class, 'edit'])->name('existing-occupant.edit');
    Route::post('existing-occupant-edit/{id}', [ExistingOccupantController::class, 'update'])->name('existing-occupant.update');
    Route::put('existing-occupant-edit/{id}', [ExistingOccupantController::class, 'update'])->name('existing-occupant.update.put');
    Route::get('existing-occupant-draft-edit/{id}', [ExistingOccupantController::class, 'editDraft'])->name('existing-occupant.edit-draft');
    Route::post('existing-occupant-draft-edit/{id}', [ExistingOccupantController::class, 'updateDraft'])->name('existing-occupant.update-draft');
    Route::put('existing-occupant-draft-edit/{id}', [ExistingOccupantController::class, 'updateDraft'])->name('existing-occupant.update-draft.put');
    Route::post('rhe-wise-flat-occupant-delete/{type}/{id}/{flat_id}', [ExistingOccupantController::class, 'destroy'])->name('existing-occupant.destroy');
});

// Existing Applicant VS/CS (Floor Shifting / Category Shifting) - Matching Drupal URLs exactly
Route::middleware(\App\Http\Middleware\CheckSessionAuth::class)->group(function () {
    Route::get('legacy-vs-cs', [ExistingApplicantVsCsController::class, 'flatWiseForm'])->name('existing-applicant-vs-cs.flat-wise-form');
    // Note: Drupal has typo "legay" instead of "legacy" - matching exactly
    Route::get('legay-vs-or-cs-form/{uid}',  
    [ExistingApplicantVsCsController::class, 'create']
)->where('uid', '.*')->name('existing-applicant-vs-cs.create');

Route::post('legay-vs-or-cs-form/{uid}',  
    [ExistingApplicantVsCsController::class, 'store']
)->where('uid', '.*')->name('existing-applicant-vs-cs.store');

    Route::get('legacy-vs-list-wohrms', [ExistingApplicantVsCsController::class, 'vsListWithoutHrms'])->name('existing-applicant-vs-cs.vs-list-without-hrms');
    Route::get('legacy-vs-wohrms-edit/{id}', [ExistingApplicantVsCsController::class, 'edit'])->name('existing-applicant-vs-cs.vs-edit-without-hrms');
    Route::post('legacy-vs-wohrms-edit/{id}', [ExistingApplicantVsCsController::class, 'update'])->name('existing-applicant-vs-cs.vs-update-without-hrms');
    Route::put('legacy-vs-wohrms-edit/{id}', [ExistingApplicantVsCsController::class, 'update'])->name('existing-applicant-vs-cs.vs-update-without-hrms.put');
    Route::get('legacy-vs-list-whrms', [ExistingApplicantVsCsController::class, 'vsListWithHrms'])->name('existing-applicant-vs-cs.vs-list-with-hrms');
    Route::get('legacy-vs-whrms-edit/{id}', [ExistingApplicantVsCsController::class, 'edit'])->name('existing-applicant-vs-cs.vs-edit-with-hrms');
    Route::post('legacy-vs-whrms-edit/{id}', [ExistingApplicantVsCsController::class, 'update'])->name('existing-applicant-vs-cs.vs-update-with-hrms');
    Route::put('legacy-vs-whrms-edit/{id}', [ExistingApplicantVsCsController::class, 'update'])->name('existing-applicant-vs-cs.vs-update-with-hrms.put');
    Route::get('legacy-cs-list-wohrms', [ExistingApplicantVsCsController::class, 'csListWithoutHrms'])->name('existing-applicant-vs-cs.cs-list-without-hrms');
    Route::get('legacy-cs-wohrms-edit/{id}', [ExistingApplicantVsCsController::class, 'edit'])->name('existing-applicant-vs-cs.cs-edit-without-hrms');
    Route::post('legacy-cs-wohrms-edit/{id}', [ExistingApplicantVsCsController::class, 'update'])->name('existing-applicant-vs-cs.cs-update-without-hrms');
    Route::put('legacy-cs-wohrms-edit/{id}', [ExistingApplicantVsCsController::class, 'update'])->name('existing-applicant-vs-cs.cs-update-without-hrms.put');
    Route::get('legacy-cs-list-whrms', [ExistingApplicantVsCsController::class, 'csListWithHrms'])->name('existing-applicant-vs-cs.cs-list-with-hrms');
    Route::get('legacy-cs-whrms-edit/{id}', [ExistingApplicantVsCsController::class, 'edit'])->name('existing-applicant-vs-cs.cs-edit-with-hrms');
    Route::post('legacy-cs-whrms-edit/{id}', [ExistingApplicantVsCsController::class, 'update'])->name('existing-applicant-vs-cs.cs-update-with-hrms');
    Route::put('legacy-cs-whrms-edit/{id}', [ExistingApplicantVsCsController::class, 'update'])->name('existing-applicant-vs-cs.cs-update-with-hrms.put');
});

// Estate Treasury Mapping - Matching Drupal URLs
Route::middleware(\App\Http\Middleware\CheckSessionAuth::class)->group(function () {
    Route::get('estate-treasury-selection', [EstateTreasuryMappingController::class, 'index'])->name('estate-treasury-selection.index');
    Route::get('estate-treasury-selection/add', [EstateTreasuryMappingController::class, 'create'])->name('estate-treasury-selection.create');
    Route::post('estate-treasury-selection/add', [EstateTreasuryMappingController::class, 'store'])->name('estate-treasury-selection.store');
    Route::get('estate-treasury-selection/edit/{id}', [EstateTreasuryMappingController::class, 'edit'])->name('estate-treasury-selection.edit');
    Route::post('estate-treasury-selection/edit/{id}', [EstateTreasuryMappingController::class, 'update'])->name('estate-treasury-selection.update');
    Route::put('estate-treasury-selection/edit/{id}', [EstateTreasuryMappingController::class, 'update'])->name('estate-treasury-selection.update.put');
    Route::get('estate-treasury-selection/delete/{id}', [EstateTreasuryMappingController::class, 'destroy'])->name('estate-treasury-selection.destroy');
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
