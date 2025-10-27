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
    //Single registration
    Route::post('/step1', [ParticipantRegistrant::class, 'step1'])->name('form.step1');
    Route::post('/step2', [ParticipantRegistrant::class, 'step2'])->name('form.step2');
    Route::post('/step3', [ParticipantRegistrant::class, 'step3'])->name('form.step3');
    Route::get('/previous', [ParticipantRegistrant::class, 'previous'])->name('form.previous');

    //Session registration
    Route::post('/add-accompagning-participant', [AccompagningRegistrant::class, 'store'])->name('add.accompagning.form');
    //
    Route::get('/invoices/download/{participant}', [InvoiceController::class, 'downloadByParticipant'])
        ->name('invoices.download.participant');

    //Group registration
    Route::get('participants/import', [GroupeRegistrant::class, 'showImportForm'])->name('participants.importForm');
    Route::get('participants/download-template', [GroupeRegistrant::class, 'downloadTemplate'])->name('participants.downloadTemplate');
    Route::post('participants/import', [GroupeRegistrant::class, 'storeMultiple'])->name('participants.store.multiple');
});

//redirect admin/login to /login
// web.php


//changer de langue
Route::get('lang/{lang}', [HomeController::class, 'switch'])->name('lang.switch');

Route::get('/home', [HomeController::class, 'index'])->name('home')->middleware(['auth', 'verified']);
