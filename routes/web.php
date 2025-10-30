<?php

use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\Registration\AccompagningRegistrant;
use App\Http\Controllers\Registration\GroupeRegistrant;
use App\Http\Controllers\Registration\ParticipantRegistrant;
use App\Http\Controllers\Voyager\AdminController;
use App\Http\Controllers\Voyager\VoyagerUserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Facades\Voyager;

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

Route::get('/', function () {
    return redirect('/home');
});

Auth::routes(['verify' => true]);

Route::group(['prefix' => 'admin'], function () {

    Voyager::routes();

    //Admin change langue
    Route::post('/locale', [HomeController::class, 'changeLocale'])->name('admin.change.locale');

    //Single registration
    Route::post('/step1', [ParticipantRegistrant::class, 'step1'])->name('form.step1');
    Route::post('/step2', [ParticipantRegistrant::class, 'step2'])->name('form.step2');
    Route::post('/step3', [ParticipantRegistrant::class, 'step3'])->name('form.step3');
    Route::get('/previous', [ParticipantRegistrant::class, 'previous'])->name('form.previous');

    //Session registration

    Route::get('/invoices/download/{participant}', [InvoiceController::class, 'downloadByParticipant'])
        ->name('invoices.download.participant');


    //Route::get('participants/import', [GroupeRegistrant::class, 'showImportForm'])->name('participants.importForm');
    Route::post('participants-add-group', [GroupeRegistrant::class, 'store'])->name('participants.store.group');
    Route::get('/participant/{uuid}/edit', [GroupeRegistrant::class, 'edit'])->name('participant.edit');
    Route::post('/participants/update', [GroupeRegistrant::class, 'update'])->name('participant.update');
    Route::get('/participant/{uuid}', [GroupeRegistrant::class, 'destroy'])->name('participant.destroy');


    //Route for accompagning registration
    Route::post('/add-accompagning-participant', [AccompagningRegistrant::class, 'store'])->name('add.accompagning.form');
    Route::get('/accompagning_participant/{uuid}/edit', [AccompagningRegistrant::class, 'edit'])->name('accompagning.edit');
    Route::post('/accompagning/update', [AccompagningRegistrant::class, 'update'])->name('accompagning.update.participant');
    Route::put('/accompagning/{uuid}', [AccompagningRegistrant::class, 'destroy'])->name('accompagning.destroy');
});


//changer de langue
Route::get('lang/{lang}', [HomeController::class, 'switch'])->name('lang.switch');


Route::get('/home', [HomeController::class, 'index'])->name('home')->middleware(['auth', 'verified']);


//Invoices Managements
Route::get('/participants/invoices', [InvoiceController::class, 'index'])->name('participants.invoices.index');
Route::post('/participants/invoices/export', [InvoiceController::class, 'export'])->name('participants.invoices.export');
