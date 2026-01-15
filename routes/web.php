<?php

use App\Http\Controllers\Web\AdminController;
use App\Http\Controllers\Web\DocumentController;
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
use App\Http\Controllers\Web\CommonApplicationController;
use App\Http\Controllers\Web\ApplicationListController;
use App\Http\Controllers\Web\ApplicationStatusController;
use App\Http\Controllers\Web\OnlineApplicationController;

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

// User SSO Login Routes
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

// New Application Form (extends common application)
Route::middleware(\App\Http\Middleware\CheckSessionAuth::class)->group(function () {
    Route::get('new-apply', [\App\Http\Controllers\Web\NewApplicationController::class, 'create'])->name('new-application.create');
    Route::post('new-apply', [\App\Http\Controllers\Web\NewApplicationController::class, 'store'])->name('new-application.store');
    
    // Category Shifting (CS) Application
    Route::get('cs', [\App\Http\Controllers\Web\CategoryShiftingController::class, 'create'])->name('category-shifting.create');
    Route::post('cs', [\App\Http\Controllers\Web\CategoryShiftingController::class, 'store'])->name('category-shifting.store');
    
    // Vertical Shifting (VS) Application
    Route::get('vs', [\App\Http\Controllers\Web\VerticalShiftingController::class, 'create'])->name('vertical-shifting.create');
    Route::post('vs', [\App\Http\Controllers\Web\VerticalShiftingController::class, 'store'])->name('vertical-shifting.store');
    
    // Allotment List Routes
    Route::get('allotment_list', [\App\Http\Controllers\Web\AllotmentListController::class, 'index'])->name('allotment-list.index');
    Route::post('allotment_list', [\App\Http\Controllers\Web\AllotmentListController::class, 'show'])->name('allotment-list.show');
    Route::get('allotment_list_approve', [\App\Http\Controllers\Web\AllotmentListController::class, 'approve'])->name('allotment-list.approve');
    Route::post('allotment_list_approve', [\App\Http\Controllers\Web\AllotmentListController::class, 'updateStatus'])->name('allotment-list.update-status');
    Route::get('allotment_list_hold', [\App\Http\Controllers\Web\AllotmentListController::class, 'hold'])->name('allotment-list.hold');
    Route::get('allotment_details/{encrypted_app_id}', [\App\Http\Controllers\Web\AllotmentListController::class, 'detail'])
        ->where('encrypted_app_id', '.*')
        ->name('allotment-list.detail');
    
    // Generate Allotment Letter Routes
    Route::get('generate_allotment_letter', [\App\Http\Controllers\Web\GenerateAllotmentLetterController::class, 'index'])->name('generate-allotment-letter.index');
    Route::post('generate_letter', [\App\Http\Controllers\Web\GenerateAllotmentLetterController::class, 'generate'])->name('generate-allotment-letter.generate');
    
    // View Allotment Details Routes
    Route::get('view_allotment_details', [\App\Http\Controllers\Web\ViewAllotmentDetailsController::class, 'index'])->name('view-allotment-details.index');
    Route::get('status_update/{encrypted_app_id}/{encrypted_status}', [\App\Http\Controllers\Web\ViewAllotmentDetailsController::class, 'updateStatus'])
        ->where(['encrypted_app_id' => '.*', 'encrypted_status' => '.*'])
        ->name('view-allotment-details.update-status');
    Route::get('download-and-upload/{encrypted_app_id}', [\App\Http\Controllers\Web\ViewAllotmentDetailsController::class, 'declaration'])
        ->where('encrypted_app_id', '.*')
        ->name('view-allotment-details.declaration');
    Route::post('download-and-upload/{encrypted_app_id}', [\App\Http\Controllers\Web\ViewAllotmentDetailsController::class, 'submitDeclaration'])
        ->where('encrypted_app_id', '.*')
        ->name('view-allotment-details.submit-declaration');
    
    // View Allotment Letter Routes
    Route::get('view_proposed_rhe', [\App\Http\Controllers\Web\ViewAllotmentLetterController::class, 'index'])->name('view-allotment-letter.index');
    Route::get('update_allotment/{encrypted_app_id}/{encrypted_status}', [\App\Http\Controllers\Web\ViewAllotmentLetterController::class, 'updateAllotment'])
        ->where(['encrypted_app_id' => '.*', 'encrypted_status' => '.*'])
        ->name('view-allotment-letter.update-allotment');
    
    // RHE Allotment Routes
    Route::get('rhe_allotment', [\App\Http\Controllers\Web\RheAllotmentController::class, 'index'])->name('rhe-allotment.index');
    Route::post('rhe_allotment/show-vacancy', [\App\Http\Controllers\Web\RheAllotmentController::class, 'showVacancy'])->name('rhe-allotment.show-vacancy');
    Route::post('rhe_allotment/process', [\App\Http\Controllers\Web\RheAllotmentController::class, 'processAllotment'])->name('rhe-allotment.process');
    
    // AJAX endpoints for new application
    Route::get('new-application/flat-type-categories', [\App\Http\Controllers\Web\NewApplicationController::class, 'getFlatTypeAndCategoriesAjax'])->name('new-application.flat-type-categories');
    Route::get('new-application/housing-estates', [\App\Http\Controllers\Web\NewApplicationController::class, 'getHousingEstatesAjax'])->name('new-application.housing-estates');
    
    // Common Application AJAX endpoints (for other forms)
    Route::get('common-application/ddo-designations', [CommonApplicationController::class, 'getDdoDesignationsAjax'])->name('common-application.ddo-designations');
    Route::get('common-application/ddo-address', [CommonApplicationController::class, 'getDdoAddressAjax'])->name('common-application.ddo-address');

    // Application List (for applicants)
    Route::get('application-list', [ApplicationListController::class, 'index'])->name('application-list.index');
    Route::get('view-application-details/{id}', [ApplicationListController::class, 'view'])->where('id', '.*')->name('application.view');

    // View Application List Module Routes
    Route::get('view_application_list/{status}/{url}/{page_status}', [ApplicationListController::class, 'dashboard'])
        ->where(['status' => '.*', 'url' => '.*', 'page_status' => '.*'])
        ->name('view_application_list.dashboard');
    Route::get('view_application/{status}/{entity}/{page_status}', [ApplicationListController::class, 'adminList'])
        ->where(['status' => '.*', 'entity' => '.*', 'page_status' => '.*'])
        ->name('view_application');
    Route::get('application_detail/{id}/{page_status}/{status}', [ApplicationListController::class, 'adminView'])
        ->where(['id' => '.*', 'page_status' => '.*', 'status' => '.*'])
        ->name('application_detail');
    Route::get('application_detail_pdf/{id}/{status}', [ApplicationListController::class, 'generateApplicationPdf'])
        ->where(['id' => '.*', 'status' => '.*'])
        ->name('application_detail_pdf');
    Route::post('update_status/{id}/{new_status}/{status}/{entity}', [ApplicationListController::class, 'updateStatus'])
        ->where(['id' => '.*', 'new_status' => '.*', 'status' => '.*', 'entity' => '.*'])
        ->name('update_status');
    Route::post('update_status/{id}/{new_status}/{status}/{entity}/{computer_serial_no}', [ApplicationListController::class, 'updateStatus'])
        ->where(['id' => '.*', 'new_status' => '.*', 'status' => '.*', 'entity' => '.*', 'computer_serial_no' => '.*'])
        ->name('update_status_with_serial');
    // Route::get('application-approve/{id}/{status}/{entity}/{page_status}/{computer_serial_no}/{flat_type}', [ApplicationListController::class, 'showApproveForm'])
    //     ->where(['id' => '.*', 'status' => '.*', 'entity' => '.*', 'page_status' => '.*', 'computer_serial_no' => '.*', 'flat_type' => '.*'])
    //     ->name('application-approve');
    Route::post('application-approve', [ApplicationListController::class, 'ddoAcceptStore'])->name('application-approve.store');
    Route::post('reject-application', [ApplicationListController::class, 'rejectApplication'])->name('reject-application');
    Route::get('download_licence_pdf/{id}', [ApplicationListController::class, 'downloadLicensePdf'])
        ->where('id', '.*')
        ->name('download_licence_pdf');

    // Application List (for admins/officials) - Alternative routes
    // Route::get('view-application-list/{status}/{entity}', [ApplicationListController::class, 'adminList'])->where(['status' => '.*', 'entity' => '.*'])->name('application-list.admin-list');
    Route::get('view-application-list/{status}/{entity}/{page_status}', [ApplicationListController::class, 'adminList'])->where(['status' => '.*', 'entity' => '.*'])->name('application-list.admin-list-with-status');
    Route::get('application-detail/{id}/{page_status}/{status}', [ApplicationListController::class, 'adminView'])->where(['id' => '.*', 'status' => '.*'])->name('application-detail.admin-view');
    Route::post('update-status/{id}/{new_status}/{status}/{entity}', [ApplicationListController::class, 'updateStatus'])->where(['id' => '.*', 'new_status' => '.*', 'status' => '.*', 'entity' => '.*'])->name('application-list.update-status');
    Route::post('update-status/{id}/{new_status}/{status}/{entity}/{computer_serial_no}', [ApplicationListController::class, 'updateStatus'])->where(['id' => '.*', 'new_status' => '.*', 'status' => '.*', 'entity' => '.*', 'computer_serial_no' => '.*'])->name('application-list.update-status-with-serial');

    // License Management
    Route::get('generate-license/{id}/{page_status}/{status}', [ApplicationListController::class, 'generateLicense'])->where(['id' => '.*', 'status' => '.*'])->name('license.generate');
    Route::get('view-generated-license', [ApplicationListController::class, 'licenseList'])->name('license.list');
    
    //DDO Specific Application Lists
    Route::get('view-flat-possession-taken-ddo', [ApplicationListController::class, 'flatPossessionTaken'])->name('flat-possession-taken');
    Route::get('view-flat-released-ddo', [ApplicationListController::class, 'flatReleased'])->name('flat-released');

    // Application Status (for applicants)
    Route::get('application_status', [ApplicationStatusController::class, 'index'])->name('application-status.index');

    // Application Status Check (for officials)
    Route::get('application_status_check', [ApplicationStatusController::class, 'checkIndex'])->name('application-status-check.index');
    Route::post('application_status_check', [ApplicationStatusController::class, 'checkSearch'])->name('application-status-check.search');
    Route::get('common-application-view/{id}/{status}', [ApplicationStatusController::class, 'viewList'])->where(['id' => '.*', 'status' => '.*'])->name('application-status-check.view-list');
    Route::get('common-application-view-det/{id}/{status}', [ApplicationStatusController::class, 'viewDetail'])->where(['id' => '.*', 'status' => '.*'])->name('application-status-check.view-detail');
    Route::get('add-possession-det/{id}/{status}', [ApplicationStatusController::class, 'showAddPossessionForm'])->where(['id' => '.*', 'status' => '.*'])->name('application-status-check.add-possession');
    Route::post('add-possession-det/{id}/{status}', [ApplicationStatusController::class, 'storePossessionDate'])->where(['id' => '.*', 'status' => '.*'])->name('application-status-check.store-possession');
    Route::get('add-release-date/{id}/{status}', [ApplicationStatusController::class, 'showAddReleaseForm'])->where(['id' => '.*', 'status' => '.*'])->name('application-status-check.add-release');
    Route::post('add-release-date/{id}/{status}', [ApplicationStatusController::class, 'storeReleaseDate'])->where(['id' => '.*', 'status' => '.*'])->name('application-status-check.store-release');
    Route::get('request-for-license-extension/{id}/{status}/{uid}/{official_detail_id}', [ApplicationStatusController::class, 'showLicenseExtensionForm'])->where(['id' => '.*', 'status' => '.*', 'uid' => '.*', 'official_detail_id' => '.*'])->name('application-status-check.license-extension');
    Route::post('request-for-license-extension/{id}/{status}/{uid}/{official_detail_id}', [ApplicationStatusController::class, 'storeLicenseExtension'])->where(['id' => '.*', 'status' => '.*', 'uid' => '.*', 'official_detail_id' => '.*'])->name('application-status-check.store-license-extension');
    Route::get('request-for-offer-letter-extension/{id}/{status}/{uid}/{official_detail_id}/{date_of_verified}', [ApplicationStatusController::class, 'showOfferLetterExtensionForm'])->where(['id' => '.*', 'status' => '.*', 'uid' => '.*', 'official_detail_id' => '.*', 'date_of_verified' => '.*'])->name('application-status-check.offer-letter-extension');
    Route::post('request-for-offer-letter-extension/{id}/{status}/{uid}/{official_detail_id}/{date_of_verified}', [ApplicationStatusController::class, 'storeOfferLetterExtension'])->where(['id' => '.*', 'status' => '.*', 'uid' => '.*', 'official_detail_id' => '.*', 'date_of_verified' => '.*'])->name('application-status-check.store-offer-letter-extension');

    // Online Application landing (parity with Drupal)
    Route::get('online_application/{url?}', [OnlineApplicationController::class, 'index'])
        ->where('url', '.*')
        ->name('online_application');
});

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

// Existing Applicant (Legacy/Physical Applicants) Routes
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

// Existing Occupant Routes
Route::middleware(\App\Http\Middleware\CheckSessionAuth::class)->group(function () {
    Route::get('rhewise_flatlist', [ExistingOccupantController::class, 'index'])->name('existing-occupant.index');
    Route::get('existing-occupant/flat-list', [ExistingOccupantController::class, 'flatList'])->name('existing-occupant.flat-list');
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

// Existing Applicant VS/CS (Floor Shifting / Category Shifting) Routes exactly
Route::middleware(\App\Http\Middleware\CheckSessionAuth::class)->group(function () {
    Route::get('legacy-vs-cs', [ExistingApplicantVsCsController::class, 'flatWiseForm'])->name('existing-applicant-vs-cs.flat-wise-form');
    // Note: Drupal has typo "legay" instead of "legacy" - matching exactly
    Route::get('legay-vs-or-cs-form/{uid}',  
    [ExistingApplicantVsCsController::class, 'create']
    )->where('uid', '.*')->name('existing-applicant-vs-cs.create');

    Route::post('legay-vs-or-cs-form/{uid}',  
        [ExistingApplicantVsCsController::class, 'store']
    )->where('uid', '.*')->name('existing-applicant-vs-cs.store');

    // Generic edit/update routes (for merged form)
    Route::get('existing-applicant-vs-cs/edit/{id}', [ExistingApplicantVsCsController::class, 'edit'])->name('existing-applicant-vs-cs.edit');
    Route::post('existing-applicant-vs-cs/edit/{id}', [ExistingApplicantVsCsController::class, 'update'])->name('existing-applicant-vs-cs.update');
    Route::put('existing-applicant-vs-cs/edit/{id}', [ExistingApplicantVsCsController::class, 'update'])->name('existing-applicant-vs-cs.update.put');

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

// Estate Treasury Mapping Routes
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

Route::get('/download-supporting-doc', [DocumentController::class, 'download'])
    ->name('supporting-doc.download');

Route::get('/view-document/{path}', [DocumentController::class, 'view'])
    ->where('path', '.*')
    ->name('document.view');






/* Frontend URL End */
