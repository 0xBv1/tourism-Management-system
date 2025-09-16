<?php

use App\Http\Controllers\Dashboard\AutoTranslationController;
use App\Http\Controllers\Dashboard\BlogController;
use App\Http\Controllers\Dashboard\BookingController;
use App\Http\Controllers\Dashboard\CarRentalController;
use App\Http\Controllers\Dashboard\CarRouteController;
use App\Http\Controllers\Dashboard\CategoryController;
use App\Http\Controllers\Dashboard\ClientController;
use App\Http\Controllers\Dashboard\ContactRequestController;
use App\Http\Controllers\Dashboard\CouponController;
use App\Http\Controllers\Dashboard\CurrencyController;
use App\Http\Controllers\Dashboard\CustomTripController;
use App\Http\Controllers\Dashboard\DestinationController;
use App\Http\Controllers\Dashboard\LocationController;
use App\Http\Controllers\Dashboard\MainController;
use App\Http\Controllers\Dashboard\PageController;
use App\Http\Controllers\Dashboard\RoleController;
use App\Http\Controllers\Dashboard\SettingController;
use App\Http\Controllers\Dashboard\SitemapController;
use App\Http\Controllers\Dashboard\ServiceApprovalController;
use App\Http\Controllers\Dashboard\SupplierController;
use App\Http\Controllers\Dashboard\SupplierServiceController as AdminSupplierServiceController;

use App\Http\Controllers\Dashboard\TourController;
use App\Http\Controllers\Dashboard\TourOptionController;
use App\Http\Controllers\Dashboard\TourReviewController;
use App\Http\Controllers\Dashboard\TripController;
use App\Http\Controllers\Dashboard\TripBookingController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Dashboard\FaqController;
use App\Http\Controllers\Dashboard\BlogCategoryController;
use App\Http\Controllers\Dashboard\CustomizedTripCategoryController;
use App\Http\Controllers\Dashboard\RedirectRuleController;
use App\Http\Controllers\Dashboard\AmenityController;
use App\Http\Controllers\Dashboard\HotelController;
use App\Http\Controllers\Dashboard\RoomController;
use App\Http\Controllers\Dashboard\HotelRoomBookingController;
use App\Http\Controllers\Supplier\DashboardController as SupplierDashboardController;
use App\Http\Controllers\Supplier\ProfileController as SupplierProfileController;
use App\Http\Controllers\Supplier\WalletController as SupplierWalletController;
use App\Http\Controllers\Supplier\ServiceController as SupplierServiceController;
use App\Http\Controllers\Supplier\StatisticsController as SupplierStatisticsController;
use App\Http\Controllers\Supplier\SupplierHotelController;
use App\Http\Controllers\Supplier\SupplierRoomController;
use App\Http\Controllers\Supplier\SupplierTourController;
use App\Http\Controllers\Supplier\SupplierTripController;
use App\Http\Controllers\Supplier\SupplierTransportController;
use App\Http\Controllers\Dashboard\TransportController;
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
    Route::resource('clients', ClientController::class)->except('show');
    
    // Supplier Management (Admin)
    Route::resource('suppliers', SupplierController::class);
    Route::post('suppliers/{supplier}/toggle-verification', [SupplierController::class, 'toggleVerification'])->name('suppliers.toggle-verification');
    Route::post('suppliers/{supplier}/toggle-active', [SupplierController::class, 'toggleActive'])->name('suppliers.toggle-active');
    Route::post('suppliers/{supplier}/commission', [SupplierController::class, 'updateCommission'])->name('suppliers.update-commission');
    

    
    // Supplier Services Management (Admin)
    Route::get('supplier-services', [AdminSupplierServiceController::class, 'index'])->name('supplier-services.index');
    Route::get('supplier-services/{type}/{id}/edit', [AdminSupplierServiceController::class, 'edit'])->name('supplier-services.edit');
    Route::put('supplier-services/{type}/{id}', [AdminSupplierServiceController::class, 'update'])->name('supplier-services.update');
    
    // Service Approvals Management
    Route::resource('service-approvals', ServiceApprovalController::class)->only(['index','show']);
    Route::post('service-approvals/{serviceApproval}/approve', [ServiceApprovalController::class, 'approve'])->name('service-approvals.approve');
    Route::post('service-approvals/{serviceApproval}/reject', [ServiceApprovalController::class, 'reject'])->name('service-approvals.reject');
    Route::post('service-approvals/{serviceApproval}/update-status', [ServiceApprovalController::class, 'updateStatus'])->name('service-approvals.update-status');
    
    // Content Management
    Route::resource('destinations', DestinationController::class)->except('show');
    Route::resource('categories', CategoryController::class)->except('show');
    Route::resource('tours', TourController::class)->except('show');
    Route::resource('transports', TransportController::class);
    Route::get('tours/{tour}', [TourController::class, 'show'])->name('tours.show');
    Route::resource('tour-options', TourOptionController::class)->except('show');
    Route::resource('pages', PageController::class)->except('show');
    Route::resource('blogs', BlogController::class)->except('show');
    Route::resource('blog-categories', BlogCategoryController::class)->except('show');
    Route::resource('locations', LocationController::class)->except('show');
    Route::resource('faqs', FaqController::class)->except('show');
    
    // Hotel Management
    Route::resource('hotels', HotelController::class)->except('show');
    Route::resource('rooms', RoomController::class)->except('show');
    Route::resource('amenities', AmenityController::class)->except('show');
    Route::resource('hotel_room_bookings', HotelRoomBookingController::class);
    
    // Car Rental Management
    Route::resource('car-routes', CarRouteController::class)->except('show');
    Route::post('car-routes/import', [CarRouteController::class, 'import'])->name('car-routes.import');
    Route::get('car-routes/template', [CarRouteController::class, 'template'])->name('car-routes.template');
    
    Route::group(['prefix' => 'car-rentals', 'as' => 'car-rentals.'], function () {
        Route::get('/', [CarRentalController::class, 'index'])->name('index');
        Route::get('/{carRental}', [CarRentalController::class, 'show'])->name('show');
    });
    
    // Custom Trips Management
    Route::group(['prefix' => 'custom-trips', 'as' => 'custom-trips.'], function () {
        Route::get('/', [CustomTripController::class, 'index'])->name('index');
        Route::get('/{customTrip}', [CustomTripController::class, 'show'])->name('show');
        Route::put('/{customTrip}', [CustomTripController::class, 'assign'])->name('assign');
    });
    
    Route::resource('customized-trip-categories', CustomizedTripCategoryController::class)->except('show');
    
    // Trips & Bookings Management
    Route::resource('trips', TripController::class);
    Route::get('trips/{trip}/bookings', [TripController::class, 'tripBookings'])->name('trips.trip-bookings');
    Route::post('trips/{trip}/toggle-status', [TripController::class, 'toggleStatus'])->name('trips.toggle-status');
    Route::get('trips/{trip}/details', [TripController::class, 'getTripDetails'])->name('trips.details');
    
    Route::resource('trip-bookings', TripBookingController::class);
    Route::post('trip-bookings/{tripBooking}/toggle-status', [TripBookingController::class, 'toggleStatus'])->name('trip-bookings.toggle-status');
    Route::post('trip-bookings/{tripBooking}/cancel', [TripBookingController::class, 'cancel'])->name('trip-bookings.cancel');
    Route::get('trip-bookings/export', [TripBookingController::class, 'export'])->name('trip-bookings.export');
    
    // Financial Management
    Route::resource('coupons', CouponController::class)->except('show');
    Route::resource('currencies', CurrencyController::class)->except('show');
    Route::get('currencies/rates/update', [CurrencyController::class, 'updateRates'])->name('currencies.rates.update');
    
    // Booking Management
    Route::resource('bookings', BookingController::class)->except(['create', 'store', 'edit', 'destroy']);
    Route::resource('tour-reviews', TourReviewController::class)->only('index');
    
    // Contact Management
    Route::resource('contact-requests', ContactRequestController::class)->except('show');
    Route::post('contact-requests/mark-as-spam', [ContactRequestController::class, 'markAsSpam'])
        ->name('contact-requests.mark-as-spam');
    
    // SEO & Redirects
    Route::resource('redirect-rules', RedirectRuleController::class)->except('show');
    Route::get('redirect-rules/export', [RedirectRuleController::class, 'export'])->name('redirect-rules.export');
    Route::post('redirect-rules/import', [RedirectRuleController::class, 'import'])->name('redirect-rules.import');
    
    // Settings
    Route::group(['prefix' => 'settings', 'as' => 'settings.'], function () {
        Route::get('show', [SettingController::class, 'show'])->name('show');
        Route::put('update', [SettingController::class, 'update'])->name('update');
    });
    //RoutePlace
});

// Supplier Dashboard Routes
Route::group([
    'prefix' => 'supplier',
    'middleware' => ['auth:web', 'verified'],
    'as' => 'supplier.'
], function () {
    // Supplier Dashboard
    Route::get('dashboard', [SupplierDashboardController::class, 'index'])->name('dashboard');
    
    // Profile Management
    Route::get('profile', [SupplierProfileController::class, 'index'])->name('profile.index');
    Route::get('profile/edit', [SupplierProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [SupplierProfileController::class, 'update'])->name('profile.update');
    
    // Wallet Management
    Route::get('wallet', [SupplierWalletController::class, 'index'])->name('wallet.index');
    Route::get('wallet/transaction/{transaction}', [SupplierWalletController::class, 'show'])->name('wallet.transaction');
    Route::post('wallet/withdrawal', [SupplierWalletController::class, 'requestWithdrawal'])->name('wallet.withdrawal');
    
    // Service Management
    Route::get('services', [SupplierServiceController::class, 'index'])->name('services.index');
    Route::get('services/hotels', [SupplierServiceController::class, 'hotels'])->name('services.hotels');
    Route::post('services/hotels', [SupplierServiceController::class, 'addHotel'])->name('services.add-hotel');
    Route::post('services/remove-hotel', [SupplierServiceController::class, 'removeHotel'])->name('services.remove-hotel');
    
    Route::get('services/tours', [SupplierServiceController::class, 'tours'])->name('services.tours');
    Route::get('services/trips', [SupplierServiceController::class, 'trips'])->name('services.trips');
    Route::get('services/transport', [SupplierServiceController::class, 'transport'])->name('services.transport');
    
    Route::post('services/add-service', [SupplierServiceController::class, 'addService'])->name('services.add-service');
    Route::post('services/remove-service', [SupplierServiceController::class, 'removeService'])->name('services.remove-service');
    
    // Search routes for services
    Route::get('services/search-service', [SupplierServiceController::class, 'searchService'])->name('services.search-service');
    Route::get('services/search-hotels', [SupplierServiceController::class, 'searchHotels'])->name('services.search-hotels');
    
    // Supplier Own Services Management
    Route::resource('hotels', SupplierHotelController::class);
    Route::post('hotels/{hotel}/toggle-status', [SupplierHotelController::class, 'toggleStatus'])->name('hotels.toggle-status');
    
    Route::resource('rooms', SupplierRoomController::class);
    Route::post('rooms/{room}/toggle-status', [SupplierRoomController::class, 'toggleStatus'])->name('rooms.toggle-status');
    
    Route::resource('tours', SupplierTourController::class);
    Route::post('tours/{tour}/toggle-status', [SupplierTourController::class, 'toggleStatus'])->name('tours.toggle-status');
    
    Route::resource('trips', SupplierTripController::class);
    Route::post('trips/{trip}/toggle-status', [SupplierTripController::class, 'toggleStatus'])->name('trips.toggle-status');
    Route::get('trips/{trip}/bookings', [SupplierTripController::class, 'bookings'])->name('trips.bookings');
    Route::get('trips/{trip}/details', [SupplierTripController::class, 'details'])->name('trips.details');
    Route::get('trips/{trip}/seats', [SupplierTripController::class, 'seats'])->name('trips.seats');
    
    Route::resource('transports', SupplierTransportController::class);
    Route::post('transports/{transport}/toggle-status', [SupplierTransportController::class, 'toggleStatus'])->name('transports.toggle-status');
    
    // Statistics & Reports
    Route::get('statistics', [SupplierStatisticsController::class, 'index'])->name('statistics.index');
    Route::get('statistics/bookings', [SupplierStatisticsController::class, 'bookings'])->name('statistics.bookings');
    Route::get('statistics/earnings', [SupplierStatisticsController::class, 'earnings'])->name('statistics.earnings');
    Route::get('statistics/commissions', [SupplierStatisticsController::class, 'commissions'])->name('statistics.commissions');
});
