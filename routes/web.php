<?php

use App\Http\Controllers\Admin\AdminRegistrationController;
use App\Http\Controllers\Admin\BadgeController;
use App\Http\Controllers\Admin\InvitationLetterController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\SendEmailsController;
use App\Http\Controllers\Admin\ValidationPaymentController;
use App\Http\Controllers\Admin\ValidationStudentYwpController;
use App\Http\Controllers\Admin\viewAdminRegistrationController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\Payment\PaymentsController;
use App\Http\Controllers\Registration\AccompagningRegistrant;
use App\Http\Controllers\Registration\GroupeRegistrant;
use App\Http\Controllers\Registration\ParticipantRegistrant;
use App\Http\Controllers\Registration\RegisterInviteController;
use App\Http\Controllers\Scanner\ScannerController;
use App\Http\Controllers\Voyager\AdminController;
use App\Http\Controllers\Voyager\VoyagerUserController;
use App\Mail\InvitationLetterMail;
use App\Models\InvitationLetter;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use TCG\Voyager\Facades\Voyager;

/*-
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('/login');
});

Auth::routes(['verify' => true]);

Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'verified']], function () {

    Voyager::routes();

    //Admin change langue
    Route::post('/locale', [HomeController::class, 'changeLocale'])->name('admin.change.locale');

    //Single registration
    Route::post('/step', [ParticipantRegistrant::class, 'step'])->name('form.step');
    Route::post('/step1', [ParticipantRegistrant::class, 'step1'])->name('form.step1');
    Route::post('/step2', [ParticipantRegistrant::class, 'step2'])->name('form.step2');
    Route::get('/edit-individual', [ParticipantRegistrant::class, 'editWithStep'])->name('form.edit.step');
    Route::get('/previous', [ParticipantRegistrant::class, 'previous'])->name('form.previous');

    //get participant recap
    //Route::get('/recap/{uuid}', [HomeController::class, 'recap'])->name('participant.recap');

    //Session registration
    Route::get('/send-invitation-letter/{uuid}', [InvitationLetterController::class, 'sendInvitationLetter'])->name('send.invitation.letter');

    //
    Route::get('/invoices/download/{participant}', [InvoiceController::class, 'downloadByParticipant'])
        ->name('invoices.download.participant');


    //Route::get('participants/import', [GroupeRegistrant::class, 'showImportForm'])->name('participants.importForm');
    Route::post('participants-add-group', [GroupeRegistrant::class, 'store'])->name('participants.store.group');
    Route::get('/participant/{uuid}/edit', [GroupeRegistrant::class, 'edit'])->name('participant.edit');
    Route::post('/participants/update', [GroupeRegistrant::class, 'update'])->name('participant.update.group');
    Route::get('/participant/{uuid}', [GroupeRegistrant::class, 'destroy'])->name('participant.destroy');
    Route::get('/recap/{uuid}', [GroupeRegistrant::class, 'recap'])->name('participant.recap');


    //Route for accompagning registration
    Route::post('/add-accompagning-participant', [AccompagningRegistrant::class, 'store'])->name('add.accompagning.form');
    Route::get('/accompagning_participant/{uuid}/edit', [AccompagningRegistrant::class, 'edit'])->name('accompagning.edit');
    Route::post('/accompagning/update', [AccompagningRegistrant::class, 'update'])->name('accompagning.update.participant');
    Route::put('/accompagning/{uuid}', [AccompagningRegistrant::class, 'destroy'])->name('accompagning.destroy');

    Route::get('/participants/invoices', [InvoiceController::class, 'index'])->name('participants.invoices.index');
    Route::post('/participants/invoices/export', [InvoiceController::class, 'export'])->name('participants.invoices.export');

    Route::get('/participants/details/{id}', [InvoiceController::class, 'details'])
        ->name('participants.details');
});

Route::group(['prefix' => 'auth', 'controller' => GoogleAuthController::class], function () {
    //Go to google
    Route::get('/google', 'redirectToGoogle')->name('auth.login.google');

    //Get the callback from google
    Route::get('/google/callback', 'handleGoogleCallback');
});


//changer de langue
Route::get('lang/{lang}', [HomeController::class, 'switch'])->name('lang.switch')->middleware(['middleware' => 'setLocale']);


Route::get('/send-invitation-letter/{uuid}', [InvitationLetterController::class, 'sendInvitationLetter'])->name('send.invitation.letter');

//Route::get('/home', [HomeController::class, 'index'])->name('home')->middleware(['auth', 'verified']);
/* 
// Routes pour exposants
Route::prefix('exhibitors')->group(function () {
    Route::post('/store-group', [ExhibitorController::class, 'storeGroup'])->name('exhibitors.store.group');
    Route::put('/update-group', [ExhibitorController::class, 'updateGroup'])->name('exhibitor.update.group');
    Route::get('/edit/{uuid}', [ExhibitorController::class, 'edit'])->name('exhibitor.edit');
    Route::delete('/destroy/{uuid}', [ExhibitorController::class, 'destroy'])->name('exhibitor.destroy');
});

// Routes pour sponsors
Route::prefix('sponsors')->group(function () {
    Route::post('/store-group', [SponsorController::class, 'storeGroup'])->name('sponsors.store.group');
    Route::put('/update-group', [SponsorController::class, 'updateGroup'])->name('sponsor.update.group');
    Route::get('/edit/{uuid}', [SponsorController::class, 'edit'])->name('sponsor.edit');
    Route::delete('/destroy/{uuid}', [SponsorController::class, 'destroy'])->name('sponsor.destroy');
}); */

Route::prefix('validator')->group(function () {
    Route::put('/{id}/approve', [ValidationStudentYwpController::class, 'approve'])->name('validation.approve');
    Route::put('/{id}/reject', [ValidationStudentYwpController::class, 'reject'])->name('validation.reject');
    Route::get('/{id}/details', [ValidationStudentYwpController::class, 'details'])->name('validation.details');
});


Route::prefix('payment')->group(function () {
    Route::get('/{id}/details', [ValidationPaymentController::class, 'details']);
    Route::post('approve-for-pending/{id}', [ValidationPaymentController::class, 'approve_for_pending']);
    Route::post('approve/{id}', [ValidationPaymentController::class, 'approve_payment']);
    Route::post('/group-pending', [ValidationPaymentController::class, 'approve_group_pending']);
    Route::post('/group', [ValidationPaymentController::class, 'approve_group'])->name('validation.approve_group.payment');

    Route::post('reject/{id}', [ValidationPaymentController::class, 'rejectInvoice']);
    Route::post('/reject-group', [ValidationPaymentController::class, 'reject_group'])->name('validation.reject_group.payment');
});

Route::prefix('badge')->group(function () {
    Route::get('/{id}/details', [BadgeController::class, 'view'])->name('badge.view');

    Route::put('/update/{badge}', [BadgeController::class, 'ajaxUpdate'])
        ->name('badge.ajax.update');

    Route::post('/print-selected', [BadgeController::class, 'printSelected'])
        ->name('badges.print.selected');
});

Route::get('/send_inv', function () {
    try {
        $invoice = App\Models\Invoice::latest('id')->first();
        //$mail = Mail::to($invoice->participant->email)->send(new App\Mail\Invoice\InvoiceMail($invoice));
        Mail::to($invoice->participant->email)->send(new InvitationLetterMail($invoice->participant, $invoice->participant->langue ?? 'fr'));
        return true;
    } catch (\Exception $th) {
        return $th->getMessage();
    }
});

// Routes pour le dashboard des inscrits

Route::get('/dashboard-registrations', [viewAdminRegistrationController::class, 'index'])
    ->name('voyager.dashboard.registrations');

Route::get('/dashboard-registrations/data', [viewAdminRegistrationController::class, 'getDashboardData'])
    ->name('voyager.dashboard.registrations.data');

Route::get('/dashboard-registrations/recent', [viewAdminRegistrationController::class, 'getRecentRegistrations'])
    ->name('voyager.dashboard.registrations.recent');

Route::get('/dashboard-registrations/export', [viewAdminRegistrationController::class, 'exportData'])
    ->name('voyager.dashboard.registrations.export');

Route::get('/participants/{id}/details', [viewAdminRegistrationController::class, 'getParticipantDetails'])
    ->name('voyager.participants.details');

Route::get('/participants/{id}/details', [viewAdminRegistrationController::class, 'index']);

Route::get('/test', [AdminRegistrationController::class, 'home']);


Route::prefix('scanner')->group(function () {
    Route::get('/', [ScannerController::class, 'index'])->name('scanner.index');
    Route::get('/session', [ScannerController::class, 'scan'])->name('scanner.participant');
    Route::get('/add-session/{sessionId}', [ScannerController::class, 'GotoSession'])->name('scanner.goto.session');
    Route::post('/remove-session', [ScannerController::class, 'RemoveSession'])->name('scanner.remove.session');
});

Route::prefix('payment')->group(function () {
    Route::post('/payment', [PaymentsController::class, 'pay'])->name('payment.pay');
    Route::post('/notify-webhooks', [PaymentsController::class, 'notify'])->name('payment.notify');
    Route::get('/return', [PaymentsController::class, 'return'])->name('payment.return');

    //Routes redirections
    //success
    Route::get('/success', fn() => view('payments.success'))
        ->name('payment.success');
    //pending
    Route::get('/pending', fn() => view('payments.pending'))
        ->name('payment.pending');
    //failed
    Route::get('/failed', fn() => view('payments.failed'))
        ->name('payment.failed');
});

Route::prefix('invites')->group(function () {
    Route::get('/', [RegisterInviteController::class, 'index'])->name('invites.list');
    Route::post('/', [RegisterInviteController::class, 'store'])->name('invites.store');
    Route::delete('/{id}', [RegisterInviteController::class, 'destroy']);
    Route::get('/nationalities/list', [RegisterInviteController::class, 'listCountries'])->name('nationalities.list');
});

Route::prefix('resend')->group(function () {
    Route::get('/{id}/resend-invoice', [SendEmailsController::class, 'resendInvoice'])
        ->name('participants.resend.invoice');

    Route::get('/{id}/resend-invitation', [SendEmailsController::class, 'resendInvitation'])
        ->name('participants.resend.invitation');

    Route::get('/{id}/resend-confirmation', [SendEmailsController::class, 'resendConfirmation'])
        ->name('participants.resend.confirmation');
});

// Route pour les nationalités
