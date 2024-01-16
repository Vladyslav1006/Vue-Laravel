<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\VerifyCsrfToken;
use App\Http\Controllers\FrontEndController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\JobSearchController;
use App\Http\Controllers\Admin\SigninlogController;
use App\Http\Controllers\Admin\JobRequestController;

use App\Http\Controllers\Admin\OtherPhoneController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\AllocatedBBController;
use App\Http\Controllers\Admin\BbapplicantController;
use App\Http\Controllers\Admin\FamilyProofController;
use App\Http\Controllers\Admin\LogActivityController;
use App\Http\Controllers\Webhooks\WebhooksController;
use App\Http\Controllers\Admin\OtherAddrressController;
use App\Http\Controllers\Admin\PublicholidaysController;
use App\Http\Controllers\Admin\OtherEmailController;
use App\Http\Controllers\Admin\OtherEtyController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', [FrontEndController::class, 'home'])->name('home');

Route::prefix('admin')->middleware(['auth', 'verified','2fa'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Admin/HomeView');
    })->name('dashboard');

    Route::resource('user', UserController::class);
    Route::delete('userauthdestroy', [UserController::class, 'authDestroy'])->name('user.authDestroy');


    Route::resource('role', RoleController::class);
    Route::resource('permission', PermissionController::class);

    Route::resource('activitylog', LogActivityController::class);
    Route::delete('activitylog-bulk-destroy', [LogActivityController::class, 'bulkDestroy'])->name('activitylog.bulkDestroy');
    Route::put('activitylog-field-update', [LogActivityController::class, 'fieldUpdate'])->name('activitylog.fieldUpdate');
    Route::post('/activitylog-history', [LogActivityController::class, 'history'])->name('activitylog.history');


    Route::resource('setting', SettingController::class);
    Route::get('setting-list', [SettingController::class, 'list'])->name('setting.list');
    Route::delete('setting-auth-destroy', [SettingController::class, 'authDestroy'])->name('setting.authDestroy');
    Route::put('/setting-bulk-update', [SettingController::class, 'bulkUpdate'])->name('setting.bulkUpdate');

    Route::resource('signinlog', SigninlogController::class);
    Route::delete('signinlog-bulk-destroy', [SigninlogController::class, 'bulkDestroy'])->name('signinlog.bulkDestroy');
    Route::get('/profile', [UserController::class, 'profile'])->name('profile.profile');
    Route::put('/update-profile', [UserController::class, 'updateProfile'])->name('profile.updateProfile');

    Route::resource('jobrequest', JobRequestController::class);
    Route::delete('jobrequest-bulk-destroy', [JobRequestController::class, 'bulkDestroy'])->name('jobrequest.bulkDestroy');
    Route::get('jobrequest/{id}/replicate', [JobRequestController::class, 'replicate'])->name('jobrequest.duplicate');
    Route::post('/jobrequest-field-update', [JobRequestController::class, 'fieldUpdate'])->name('jobrequest.fieldUpdate');
    Route::post('/jobrequest-crdr-update', [JobRequestController::class, 'assignCRDR'])->name('jobrequest.assignCRDR');
    Route::post('jobrequest-bulkAccept', [JobRequestController::class, 'bulkAccept'])->name('jobrequest.bulkAccept');
    Route::post('jobrequest-bulkReject', [JobRequestController::class, 'bulkReject'])->name('jobrequest.bulkReject');
    Route::post('jobrequest-bulkDuplicate', [JobRequestController::class, 'bulkDuplicate'])->name('jobrequest.bulkDuplicate');
    Route::get('jobrequest-export', [JobRequestController::class, 'export'])->name('jobrequest.export');
    Route::get('jobrequest-field-data', [JobRequestController::class, 'fieldData'])->name('jobrequest.fieldData');

    Route::get('dbbackup', [SettingController::class, 'dbBackup'])->name('dbbackup.download');

    Route::resource('bbapplicant', BbapplicantController::class);
    Route::delete('bbapplicant-bulk-destroy', [BbapplicantController::class, 'bulkDestroy'])->name('bbapplicant.bulkDestroy');
    Route::get('bbapplicant/{id}/replicate', [BbapplicantController::class, 'replicate'])->name('bbapplicant.duplicate');
    Route::post('/bbapplicant-field-update', [BbapplicantController::class, 'fieldUpdate'])->name('bbapplicant.fieldUpdate');
    Route::post('bbapplicant-bulkAccept', [BbapplicantController::class, 'bulkAccept'])->name('bbapplicant.bulkAccept');
    Route::post('bbapplicant-bulkReject', [BbapplicantController::class, 'bulkReject'])->name('bbapplicant.bulkReject');
    Route::post('bbapplicant-bulkDuplicate', [BbapplicantController::class, 'bulkDuplicate'])->name('bbapplicant.bulkDuplicate');
    Route::get('bbapplicant-export', [BbapplicantController::class, 'export'])->name('bbapplicant.export');
    Route::get('bbapplicant-field-data', [BbapplicantController::class, 'fieldData'])->name('bbapplicant.fieldData');



    //public holidays
    Route::resource('public-holidays', PublicholidaysController::class);
    Route::post('public-holidays-bulkDuplicate', [PublicholidaysController::class, 'bulkDuplicate'])->name('public-holidays.bulkDuplicate');
    Route::delete('public-holidays-bulk-destroy', [PublicholidaysController::class, 'bulkDestroy'])->name('public-holidays.bulkDestroy');
    Route::get('public-holidays-import-file', [PublicholidaysController::class, 'importFile'])->name('public-holidays.import-file');
    Route::post('public-holidays-import-file-submit', [PublicholidaysController::class, 'importFileSubmit'])->name('public-holidays.import-file-submit');
    Route::get('public-holidays-saveExportActivityLog', [PublicholidaysController::class, 'saveExportActivityLog'])->name('public-holidays.saveExportActivityLog');

    Route::resource('jobsearch', JobSearchController::class);
    Route::delete('jobsearch-bulk-destroy', [JobSearchController::class, 'bulkDestroy'])->name('jobsearch.bulkDestroy');
    Route::get('jobsearch/{id}/replicate', [JobSearchController::class, 'replicate'])->name('jobsearch.duplicate');
    Route::post('/jobsearch-field-update', [JobSearchController::class, 'fieldUpdate'])->name('jobsearch.fieldUpdate');
    Route::post('/jobsearch-crdr-update', [JobSearchController::class, 'assignCRDR'])->name('jobsearch.assignCRDR');
    Route::post('jobsearch-bulkAccept', [JobSearchController::class, 'bulkAccept'])->name('jobsearch.bulkAccept');
    Route::post('jobsearch-bulkReject', [JobSearchController::class, 'bulkReject'])->name('jobsearch.bulkReject');
    Route::post('jobsearch-bulkDuplicate', [JobSearchController::class, 'bulkDuplicate'])->name('jobsearch.bulkDuplicate');
    Route::post('jobsearch-geneinv', [JobSearchController::class, 'geneInv'])->name('jobsearch.geneinv');
    Route::get('jobsearch-export', [JobSearchController::class, 'export'])->name('jobsearch.export');
    Route::post('jobsearch-updatelcn', [JobSearchController::class, 'updateLcn'])->name('jobsearch.updateLcn');
    Route::get('jobsearch-field-data', [JobSearchController::class, 'fieldData'])->name('jobsearch.fieldData');

    Route::post('/other-phone', [OtherPhoneController::class, 'otherPhone'])->name('otherphone.list');
    Route::post('/other-phone-update', [OtherPhoneController::class, 'replace'])->name('otherphone.replace');

    Route::post('/other-address', [OtherAddrressController::class, 'otherAddress'])->name('otheraddress.list');
    Route::post('/other-address-update', [OtherAddrressController::class, 'replace'])->name('otheraddress.replace');

    Route::post('/family-proof', [FamilyProofController::class, 'familyProofs'])->name('familyproof.list');
    Route::post('/family-proof-update', [FamilyProofController::class, 'replace'])->name('familyproof.replace');

    Route::post('/other-email', [OtherEmailController::class, 'otherEmail'])->name('otheremail.list');
    Route::post('/other-email-update', [OtherEmailController::class, 'replace'])->name('otheremail.replace');

    Route::post('/other-ety', [OtherEtyController::class, 'otherEty'])->name('otherEty.list');
    Route::post('/other-ety-update', [OtherEtyController::class, 'replace'])->name('otherEty.replace');

    Route::post('/allocated-bb', [AllocatedBBController::class, 'allocatedbbs'])->name('allocatedbb.list');
    Route::post('/allocated-bb-save', [AllocatedBBController::class, 'save'])->name('allocatedbb.save');
    Route::post('/allocated-bb-delete', [AllocatedBBController::class, 'delete'])->name('allocatedbb.delete');
    Route::post('/allocated-bb-babysitter', [AllocatedBBController::class, 'getCandidates'])->name('allocatedbb.babysitter');
    Route::post('/allocated-bb-babysitterall', [AllocatedBBController::class, 'getCandidatesAll'])->name('allocatedbb.babysitterall');
    Route::post('/allocated-bb-changerank', [AllocatedBBController::class, 'changeRank'])->name('allocatedbb.changerank');
    Route::post('/allocated-bb-changeranknum', [AllocatedBBController::class, 'changeRankNum'])->name('allocatedbb.changeranknum');
    Route::post('/allocated-bb-changeremark', [AllocatedBBController::class, 'changeremark'])->name('allocatedbb.changeremark');



});

Route::post('/hooks/{driver}', [WebhooksController::class, 'store'])->withoutMiddleware(VerifyCsrfToken::class);

Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);

require __DIR__ . '/auth.php';
