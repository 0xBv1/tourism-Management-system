<?php

use App\Http\Controllers\Dashboard\AutoTranslationController;
use App\Http\Controllers\Dashboard\BookingController;
use App\Http\Controllers\Dashboard\InquiryController;
use App\Http\Controllers\Dashboard\MainController;
use App\Http\Controllers\Dashboard\RoleController;
use App\Http\Controllers\Dashboard\SettingController;
use App\Http\Controllers\Dashboard\SitemapController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Dashboard\RedirectRuleController;
use App\Http\Controllers\Dashboard\HotelController;
use App\Http\Controllers\Dashboard\VehicleController;
use App\Http\Controllers\Dashboard\GuideController;
use App\Http\Controllers\Dashboard\RepresentativeController;
use App\Http\Controllers\Dashboard\ResourceAssignmentController;
use App\Http\Controllers\Dashboard\ResourceReportController;
use App\Http\Controllers\Dashboard\PaymentController;
use Illuminate\Support\Facades\Route;

//controllers
Route::group([
    'prefix' => 'dashboard',
    'middleware' => ['auth:web', 'permitted'],
    'as' => 'dashboard.'
], function () {
    
    // Profile & Theme Routes
    Route::get('toggle-theme', [ProfileController::class, 'toggleTheme'])->name('toggle-theme');
    
    // System Routes
    Route::post('cache/clear', [MainController::class, 'clearCache'])->name('cache.clear');
    Route::post('translate', [AutoTranslationController::class, 'translate'])->name('model.auto.translate');
    Route::get('sitemap/generate', SitemapController::class)->name('sitemap.generate');
    
    // User Management
    Route::resource('users', UserController::class)->except('show');
    Route::resource('roles', RoleController::class)->except('show');
    
    // Inquiry Management
    Route::resource('inquiries', InquiryController::class);
    Route::post('inquiries/{inquiry}/confirm', [InquiryController::class, 'confirm'])->name('inquiries.confirm');
    
    // Booking Management
    Route::resource('bookings', BookingController::class)->only(['index', 'show', 'update']);
    Route::post('bookings/{booking}/checklist', [BookingController::class, 'updateChecklist'])->name('bookings.checklist');
    Route::get('bookings/{booking}/download', [BookingController::class, 'download'])->name('bookings.download');
    Route::get('bookings/{booking}/send', [BookingController::class, 'send'])->name('bookings.send');
    
    // SEO & Redirects
    Route::resource('redirect-rules', RedirectRuleController::class)->except('show');
    Route::get('redirect-rules/export', [RedirectRuleController::class, 'export'])->name('redirect-rules.export');
    Route::post('redirect-rules/import', [RedirectRuleController::class, 'import'])->name('redirect-rules.import');
    
    // Resource Management
    Route::resource('hotels', HotelController::class);
    Route::get('hotels/calendar', [HotelController::class, 'calendar'])->name('hotels.calendar');
    
    Route::resource('vehicles', VehicleController::class);
    Route::get('vehicles/calendar', [VehicleController::class, 'calendar'])->name('vehicles.calendar');
    
    Route::resource('guides', GuideController::class);
    Route::get('guides/calendar', [GuideController::class, 'calendar'])->name('guides.calendar');
    
    Route::resource('representatives', RepresentativeController::class);
    Route::get('representatives/calendar', [RepresentativeController::class, 'calendar'])->name('representatives.calendar');
    
    // Resource Assignment
    Route::group(['prefix' => 'resource-assignments', 'as' => 'resource-assignments.'], function () {
        Route::get('{bookingFile}/create', [ResourceAssignmentController::class, 'create'])->name('create');
        Route::post('{bookingFile}', [ResourceAssignmentController::class, 'store'])->name('store');
        Route::delete('{resourceBooking}', [ResourceAssignmentController::class, 'destroy'])->name('destroy');
    });
    
    // Resource API Routes
    Route::group(['prefix' => 'resources', 'as' => 'resources.'], function () {
        Route::post('available', [ResourceAssignmentController::class, 'getAvailableResources'])->name('available');
        Route::post('check-availability', [ResourceAssignmentController::class, 'checkAvailability'])->name('check-availability');
        Route::get('utilization', [ResourceAssignmentController::class, 'getUtilizationReport'])->name('utilization');
    });
    
    // Resource Reports
    Route::group(['prefix' => 'reports', 'as' => 'reports.'], function () {
        Route::get('resource-utilization', [ResourceReportController::class, 'index'])->name('resource-utilization');
        Route::get('resource-utilization/export', [ResourceReportController::class, 'export'])->name('resource-utilization.export');
        Route::get('resource-details/{resourceType}/{resourceId}', [ResourceReportController::class, 'showResourceDetails'])->name('resource-details');
    });

    // Comprehensive Reports
    Route::group(['prefix' => 'reports', 'as' => 'reports.'], function () {
        Route::get('/', [App\Http\Controllers\Dashboard\ReportsController::class, 'index'])->name('index');
        Route::get('inquiries', [App\Http\Controllers\Dashboard\ReportsController::class, 'inquiries'])->name('inquiries');
        Route::get('bookings', [App\Http\Controllers\Dashboard\ReportsController::class, 'bookings'])->name('bookings');
        Route::get('finance', [App\Http\Controllers\Dashboard\ReportsController::class, 'finance'])->name('finance');
        Route::get('operational', [App\Http\Controllers\Dashboard\ReportsController::class, 'operational'])->name('operational');
        Route::get('performance', [App\Http\Controllers\Dashboard\ReportsController::class, 'performance'])->name('performance');
        Route::get('export/{type}', [App\Http\Controllers\Dashboard\ReportsController::class, 'export'])->name('export');
    });

    // Payment Management
    Route::resource('payments', PaymentController::class);
    Route::post('payments/{payment}/mark-as-paid', [PaymentController::class, 'markAsPaid'])->name('payments.mark-as-paid');
    Route::get('payments/statements', [PaymentController::class, 'statements'])->name('payments.statements');
    Route::get('payments/aging-buckets', [PaymentController::class, 'agingBuckets'])->name('payments.aging-buckets');

    // Settings
    Route::group(['prefix' => 'settings', 'as' => 'settings.'], function () {
        Route::get('show', [SettingController::class, 'show'])->name('show');
        Route::put('update', [SettingController::class, 'update'])->name('update');
    });
    //RoutePlace
});