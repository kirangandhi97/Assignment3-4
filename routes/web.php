<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\GuaranteeController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SampleController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/home');
    }
    return redirect('/login');
});

// Authentication routes - Laravel 12 way
Route::middleware('web')->group(function () {
    Route::get('login', [\App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [\App\Http\Controllers\Auth\LoginController::class, 'login']);
    Route::post('logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

    Route::get('register', [\App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [\App\Http\Controllers\Auth\RegisterController::class, 'register']);

    Route::get('password/reset', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('password/email', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('password/reset/{token}', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('password/reset', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');
});

// Protected routes
Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Sample file routes
    Route::get('/samples/csv', [SampleController::class, 'sampleCsv'])->name('samples.csv');
    Route::get('/samples/json', [SampleController::class, 'sampleJson'])->name('samples.json');
    Route::get('/samples/xml', [SampleController::class, 'sampleXml'])->name('samples.xml');

    // File view/download routes
    Route::get('/files/{id}/view', [FileController::class, 'viewContent'])->name('files.view-content');
    Route::get('/files/{id}/download', [FileController::class, 'downloadContent'])->name('files.download-content');

    // Guarantee routes
    Route::resource('guarantees', GuaranteeController::class);
    Route::post('guarantees/{id}/submit-for-review', [GuaranteeController::class, 'submitForReview'])->name('guarantees.submit-for-review');
    
    // Admin only guarantee routes
    Route::middleware('admin')->group(function () {
        Route::post('guarantees/{id}/apply', [GuaranteeController::class, 'applyGuarantee'])->name('guarantees.apply');
        Route::post('guarantees/{id}/issue', [GuaranteeController::class, 'issueGuarantee'])->name('guarantees.issue');
        Route::post('guarantees/{id}/reject', [GuaranteeController::class, 'rejectGuarantee'])->name('guarantees.reject');
    });

    // File routes
    Route::resource('files', FileController::class);
    
    // Admin only file routes
    Route::middleware('admin')->group(function () {
        Route::post('files/{id}/process', [FileController::class, 'process'])->name('files.process');
    });

    // Admin routes
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('dashboard');
        Route::get('/pending-reviews', [AdminController::class, 'pendingReviews'])->name('pending-reviews');
        Route::get('/file-processing', [AdminController::class, 'fileProcessing'])->name('file-processing');
        Route::get('/review/{id}', [AdminController::class, 'showReviewForm'])->name('show-review-form');
    });
});