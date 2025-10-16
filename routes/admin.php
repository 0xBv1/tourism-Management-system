<?php

use App\Http\Controllers\Dashboard\AutoTranslationController;
use App\Http\Controllers\Dashboard\BookingController;
use App\Http\Controllers\Dashboard\ChatController;
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
use App\Http\Controllers\Dashboard\PaymentController;
use App\Http\Controllers\Dashboard\NotificationController;


use App\Http\Controllers\Dashboard\SettlementController;
use App\DataTables\SettlementDataTable;
use App\Http\Controllers\Dashboard\InquiryResourceController;
use App\Http\Controllers\Dashboard\TicketController;
use App\Http\Controllers\Dashboard\DahabiaController;
use App\Http\Controllers\Dashboard\RestaurantController;
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
    
    // Notification Routes
    Route::get('notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::post('notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::post('notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    

    // User Management
    Route::resource('users', UserController::class)->except('show');
    Route::resource('roles', RoleController::class)->except('show');
    
    // Inquiry Management
    Route::resource('inquiries', InquiryController::class);
    Route::post('inquiries/{inquiry}/confirm', [InquiryController::class, 'confirm'])->name('inquiries.confirm');
    Route::get('inquiries/{inquiry}/confirm-form', [InquiryController::class, 'showConfirmForm'])->name('inquiries.confirm-form');
    Route::post('inquiries/{inquiry}/process-confirmation', [InquiryController::class, 'processConfirmation'])->name('inquiries.process-confirmation');
    Route::patch('inquiries/{inquiry}/tour-itinerary', [InquiryController::class, 'updateTourItinerary'])->name('inquiries.update-tour-itinerary');
    
    // Inquiry Resources Management
    Route::group(['prefix' => 'inquiries/{inquiry}', 'as' => 'inquiries.'], function () {
        Route::post('resources', [InquiryResourceController::class, 'store'])
            ->name('resources.store');
        Route::get('resources/available', [InquiryResourceController::class, 'getAvailableResources'])
            ->name('resources.available');
    });
    Route::get('inquiries/resources/{id}', [InquiryResourceController::class, 'show'])
        ->name('inquiries.resources.show');
    Route::delete('inquiries/resources/{id}', [InquiryResourceController::class, 'destroy'])
        ->name('inquiries.resources.destroy');
    
    // Chat Management
    Route::prefix('inquiries/{inquiry}')->name('inquiries.')->group(function () {
        Route::get('chats', [ChatController::class, 'index'])->name('chats.index');
        Route::post('chats', [ChatController::class, 'store'])->name('chats.store');
        Route::post('chats/mark-all-read', [ChatController::class, 'markAllAsRead'])->name('chats.mark-all-read');
    });
    Route::post('chats/{chat}/mark-read', [ChatController::class, 'markAsRead'])->name('chats.mark-read');
    Route::get('chats/recipients', [ChatController::class, 'getRecipients'])->name('chats.recipients');
    
    // Booking Management
    Route::resource('bookings', BookingController::class)->only(['index', 'show', 'update', 'destroy']);
    Route::post('bookings/{booking}/checklist', [BookingController::class, 'updateChecklist'])->name('bookings.checklist');
    Route::get('bookings/{booking}/download', [BookingController::class, 'download'])->name('bookings.download');
    Route::get('bookings/{booking}/send', [BookingController::class, 'send'])->name('bookings.send');
    
    // SEO & Redirects
    Route::resource('redirect-rules', RedirectRuleController::class)->except('show');
    Route::get('redirect-rules/export', [RedirectRuleController::class, 'export'])->name('redirect-rules.export');
    Route::post('redirect-rules/import', [RedirectRuleController::class, 'import'])->name('redirect-rules.import');
    
    // Resource Management
    // Calendar routes must be defined before resource routes to avoid conflicts
    Route::get('hotels/calendar', [HotelController::class, 'calendar'])->name('hotels.calendar');
    Route::get('vehicles/calendar', [VehicleController::class, 'calendar'])->name('vehicles.calendar');
    Route::get('guides/calendar', [GuideController::class, 'calendar'])->name('guides.calendar');
    Route::get('representatives/calendar', [RepresentativeController::class, 'calendar'])->name('representatives.calendar');
    
    // Resource routes
    Route::resource('hotels', HotelController::class);
    Route::resource('vehicles', VehicleController::class);
    Route::resource('guides', GuideController::class);
    Route::resource('representatives', RepresentativeController::class);

    // Ticket Resource Management
    Route::resource('tickets', TicketController::class);
    Route::resource('dahabias', DahabiaController::class);
    Route::resource('restaurants', RestaurantController::class);
    
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

    // Settlements
    Route::get('settlements/generate', [SettlementController::class, 'showGenerateForm'])->name('settlements.generate');
    Route::post('settlements/generate', [SettlementController::class, 'generateAutomatic'])->name('settlements.generate-automatic');
    Route::get('settlements/get-resource-bookings', [SettlementController::class, 'getResourceBookings'])->name('settlements.get-resource-bookings');
    Route::get('settlements', [SettlementController::class, 'index'])->name('settlements.index');
    Route::resource('settlements', SettlementController::class)->except(['index']);
    Route::post('settlements/{settlement}/calculate', [SettlementController::class, 'calculate'])->name('settlements.calculate');
    Route::post('settlements/{settlement}/approve', [SettlementController::class, 'approve'])->name('settlements.approve');
    Route::post('settlements/{settlement}/reject', [SettlementController::class, 'reject'])->name('settlements.reject');
    Route::post('settlements/{settlement}/mark-paid', [SettlementController::class, 'markAsPaid'])->name('settlements.mark-paid');
    
    //RoutePlace
});