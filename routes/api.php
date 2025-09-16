<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\TourController;
use App\Http\Controllers\Api\RecaptchaController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CurrencyController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\PasswordController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\Api\CarRentalController;
use App\Http\Controllers\Api\CustomTripController;
use App\Http\Controllers\Api\TourReviewController;
use App\Http\Controllers\Api\DestinationController;
use App\Http\Controllers\Api\BlogCategoryController;
use App\Http\Controllers\Api\ContactRequestController;
use App\Http\Controllers\Api\Payment\PaypalController;
use App\Http\Controllers\Api\Payment\FawaterkController;
use App\Http\Controllers\Api\CustomizedTripCategoryController;
use App\Http\Controllers\Api\AmenityController;
use App\Http\Controllers\Api\HotelController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\SupplierRoomController;
use App\Http\Controllers\Api\SupplierServiceController;
use App\Http\Controllers\Api\DurationController;
use App\Http\Controllers\Api\TripController;
use App\Http\Controllers\Api\TransportController;

//controllers
Route::group(['as' => 'api.', 'middleware' => ['api.localize']], function () {
    Route::post('/verify-recaptcha', [RecaptchaController::class, 'verify']);
    Route::group(['prefix' => 'payments', 'as' => 'payments'], function () {
        Route::group(['prefix' => 'fawaterk', 'as' => 'fawaterk.'], function () {
            Route::get('methods', [FawaterkController::class, 'methods'])->name('methods');
            Route::get('update/invoice', [FawaterkController::class, 'updateInvoice'])->name('update.invoice');
        });
        Route::group(['prefix' => 'paypal', 'as' => 'paypal.'], function () {
            Route::get('capture', [PaypalController::class, 'capture'])->name('capture');
            Route::get('cancel', [PaypalController::class, 'cancel'])->name('cancel');
        });
    });
    Route::group(['prefix' => 'cart', 'as' => 'cart.'], function () {
        Route::get('list', [CartController::class, 'list'])->name('list');
        Route::post('tours/append', [CartController::class, 'appendTour'])->name('append.tour');
        Route::post('rentals/append', [CartController::class, 'appendRental'])->name('append.rental');
        Route::delete('remove/{item}', [CartController::class, 'remove'])->name('remove');
        Route::delete('clear', [CartController::class, 'clear'])->name('clear');
    });
    Route::post('/bookings', [BookingController::class, 'create'])->name('create');
    /*Auth Middleware*/
    Route::group(['middleware' => 'auth:client'], function () {
        Route::group(['prefix' => 'wishlist', 'as' => 'wishlist.'], function () {
            Route::get('/', [WishlistController::class, 'index'])->name('index');
            Route::put('/{tour}/toggle', [WishlistController::class, 'toggle'])->name('toggle');
        });
        Route::group(['prefix' => 'profile', 'as' => 'profile'], function () {
            Route::patch('/', [ProfileController::class, 'update'])->name('update');
            Route::get('me', [ProfileController::class, 'me'])->name('me');
            Route::post('change/image', [ProfileController::class, 'changeProfileImage'])->name('change.profile.image');
            Route::post('logout', [ProfileController::class, 'logout'])->name('logout');
        });
        Route::group(['prefix' => 'coupons', 'as' => 'coupon.'], function () {
            Route::get('/{coupon:code}/validate', [CouponController::class, 'validateCoupon'])->name('validate');
        });
        Route::group(['prefix' => 'bookings', 'as' => 'bookings.'], function () {
            Route::get('/', [BookingController::class, 'index'])->name('index');
            Route::get('/{id}', [BookingController::class, 'show'])->name('show');
        });
        
    });
    // Save deal route (doesn't require authentication)
    Route::post('/booking/save-deal', [BookingController::class, 'saveDeal'])->name('booking.save-deal');
    /*Auth Middleware*/
    Route::group(['prefix' => 'auth', 'as' => 'auth.'], function () {
        Route::post('/login', [AuthController::class, 'login'])->name('login');
        Route::post('/register', [AuthController::class, 'register'])->name('register');
        Route::post('password/forget', [PasswordController::class, 'forget'])->name('client.forget.password');
        Route::post('password/reset', [PasswordController::class, 'reset'])->name('client.reset.password');
        Route::post('password/otp/verify', [PasswordController::class, 'otpVerify'])->name('client.password.otp.verify');
    });
    Route::post('custom/trips', CustomTripController::class)->name('custom.trips');
    Route::group(['prefix' => 'destinations', 'as' => 'destinations.'], function () {
        Route::get('/', [DestinationController::class, 'index'])->name('index');
        Route::get('/{slug}', [DestinationController::class, 'show'])->name('show');
    });
    Route::group(['prefix' => 'categories', 'as' => 'categories.'], function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::get('/{slug}', [CategoryController::class, 'show'])->name('show');
    });
    Route::group(['prefix' => 'tours', 'as' => 'tours.'], function () {
        Route::get('/', [TourController::class, 'index'])->name('index');
        Route::get('/stats', [TourController::class, 'stats'])->name('stats');
        Route::get('/{slug}', [TourController::class, 'show'])->name('show');
    });
    
    // Unified Hotel API Routes (regular + supplier hotels)
    Route::group(['prefix' => 'hotels', 'as' => 'hotels.'], function () {
        Route::get('/', [HotelController::class, 'index'])->name('index');
        Route::get('/{id}', [HotelController::class, 'show'])->name('show');
    });
    
    // Unified Trip API Routes (regular + supplier trips)
    Route::group(['prefix' => 'trips', 'as' => 'trips.'], function () {
        Route::get('/', [TripController::class, 'index'])->name('index');
        Route::post('/search', [TripController::class, 'search'])->name('search');
        Route::post('/book', [TripController::class, 'book'])->name('book');
        Route::get('/{id}', [TripController::class, 'show'])->name('show');
    });
    
    // Unified Transport API Routes (regular + supplier transports)
    Route::group(['prefix' => 'transport', 'as' => 'transport.'], function () {
        Route::get('/', [TransportController::class, 'index'])->name('index');
        Route::get('/stats', [TransportController::class, 'stats'])->name('stats');
        Route::get('/{id}', [TransportController::class, 'show'])->name('show');
    });
    

    
    Route::group(['prefix' => 'currencies', 'as' => 'currencies.'], function () {
        Route::get('/', [CurrencyController::class, 'index'])->name('index');
        Route::get('/{id}', [CurrencyController::class, 'show'])->name('show');
    });
    Route::group(['prefix' => 'countries', 'as' => 'countries.'], function () {
        Route::get('/', [CountryController::class, 'index'])->name('index');
        Route::get('/{id}', [CountryController::class, 'show'])->name('show');
    });
    Route::group(['prefix' => 'pages', 'as' => 'pages.'], function () {
        Route::get('/', [PageController::class, 'index'])->name('index');
        Route::get('/{key}', [PageController::class, 'show'])->name('show');
    });
    Route::group(['prefix' => 'tour-reviews', 'as' => 'tour-reviews.'], function () {
        Route::get('/', [TourReviewController::class, 'index'])->name('index');
        Route::post('/', [TourReviewController::class, 'store'])->name('store');
    });
    Route::group(['prefix' => 'contact-requests', 'as' => 'contact-requests.'], function () {
        Route::post('/', [ContactRequestController::class, 'store'])->name('store');
    });
    Route::group(['prefix' => 'blogs', 'as' => 'blogs.'], function () {
        Route::get('/', [BlogController::class, 'index'])->name('index');
        Route::get('/{id}', [BlogController::class, 'show'])->name('show');
    });
    Route::group(['prefix' => 'locations', 'as' => 'locations.'], function () {
        Route::get('/', [LocationController::class, 'index'])->name('index');
        Route::get('/{id}', [LocationController::class, 'show'])->name('show');
    });
    Route::group(['prefix' => 'car/rental', 'as' => 'car-rentals.'], function () {
        Route::post('available/destinations', [CarRentalController::class, 'searchForAvailableDestinations'])->name('available.destinations');
        Route::post('search/for/route', [CarRentalController::class, 'searchForRoute'])->name('search.for.route');
        Route::post('checkout', [CarRentalController::class, 'checkout'])->name('checkout');
    });
    Route::group(['prefix' => 'car-rentals', 'as' => 'car-rentals.'], function () {
        Route::get('/', [CarRentalController::class, 'index'])->name('index');
        Route::get('/{id}', [CarRentalController::class, 'show'])->name('show');
    });
    Route::get('settings', [SettingController::class, 'index'])->name('settings');
    Route::group(['prefix' => 'faqs', 'as' => 'faqs.'], function () {
        Route::get('/', [FaqController::class, 'index'])->name('index');
        Route::get('/{id}', [FaqController::class, 'show'])->name('show');
    });
    Route::group(['prefix' => 'blog-categories', 'as' => 'blog-categories.'], function () {
        Route::get('/', [BlogCategoryController::class, 'index'])->name('index');
        Route::get('/{id}', [BlogCategoryController::class, 'show'])->name('show');
    });
    Route::group(['prefix' => 'customized-trip-categories', 'as' => 'customized-trip-categories.'], function () {
        Route::get('/', [CustomizedTripCategoryController::class, 'index'])->name('index');
        Route::get('/{id}', [CustomizedTripCategoryController::class, 'show'])->name('show');
    });
    Route::group(['prefix' => 'amenities', 'as' => 'amenities.'], function () {
        Route::get('/', [AmenityController::class, 'index'])->name('index');
        Route::get('/{id}', [AmenityController::class, 'show'])->name('show');
    });
    Route::group(['prefix' => 'rooms', 'as' => 'rooms.'], function () {
        Route::get('/', [RoomController::class, 'index'])->name('index');
        Route::get('/{id}', [RoomController::class, 'show'])->name('show');
    });
    
    Route::group(['prefix' => 'supplier-rooms', 'as' => 'supplier-rooms.'], function () {
        Route::get('/', [SupplierRoomController::class, 'index'])->name('index');
        Route::get('/{id}', [SupplierRoomController::class, 'show'])->name('show');
        Route::get('/hotel/{hotelId}', [SupplierRoomController::class, 'byHotel'])->name('by-hotel');
    });
    
    Route::group(['prefix' => 'durations', 'as' => 'durations.'], function () {
        Route::get('/', [DurationController::class, 'index'])->name('index');
        Route::get('/{slug}', [DurationController::class, 'show'])->name('show');
    });
    
    // Supplier Services API Routes
    Route::group(['prefix' => 'supplier-services', 'as' => 'supplier-services.'], function () {
        Route::get('/', [SupplierServiceController::class, 'index'])->name('index');
        Route::get('/recommended', [SupplierServiceController::class, 'recommended'])->name('recommended');
        Route::get('/supplier/{supplierId}', [SupplierServiceController::class, 'bySupplier'])->name('by-supplier');
        Route::get('/{type}/{id}', [SupplierServiceController::class, 'show'])->name('show');
    });
    
    //RoutePlace
});
