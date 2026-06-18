<?php

use App\Http\Controllers\AccessController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\JobController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Review\ReviewController;
use App\Http\Controllers\Review\ReviewSubmissionController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('access.show'));

Route::get('/access', [AccessController::class, 'show'])->name('access.show');
Route::post('/access', [AccessController::class, 'store'])->name('access.store');

Route::get('/review/{token}', [ReviewController::class, 'show'])->name('review.show');
Route::patch('/review/{token}/blogs/{blog}', [ReviewSubmissionController::class, 'update'])->name('review.blog.update');
Route::post('/review/{token}/submit', [ReviewSubmissionController::class, 'submit'])->name('review.submit');
Route::post('/review/{token}/finalize', [ReviewSubmissionController::class, 'finalize'])->name('review.finalize');

Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('clients', ClientController::class);
    Route::resource('jobs', JobController::class);
    Route::resource('jobs.blogs', BlogController::class)->except(['index']);

    Route::post('jobs/{job}/send-for-review', [JobController::class, 'sendForReview'])->name('jobs.send-for-review');
    Route::post('jobs/{job}/prepare-re-review', [JobController::class, 'prepareReReview'])->name('jobs.prepare-re-review');
    Route::post('jobs/{job}/complete', [JobController::class, 'complete'])->name('jobs.complete');

    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::patch('notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');

    Route::get('jobs/{job}/export', [ExportController::class, 'download'])->name('jobs.export');

    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings/smtp', [SettingsController::class, 'updateSmtp'])->name('settings.smtp.update');
    Route::post('settings/test-email', [SettingsController::class, 'testEmail'])->name('settings.test-email');
    Route::post('settings/templates/{template}', [SettingsController::class, 'updateTemplate'])->name('settings.templates.update');

    Route::post('logout', [AccessController::class, 'destroy'])->name('logout');
});
