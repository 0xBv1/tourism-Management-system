<?php

use App\Events\NewBookingEvent;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

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
require __DIR__.'/auth.php';
require __DIR__.'/admin.php';

Route::redirect('/', '/login');

Route::get('/dashboard', function () {
    return view('dashboard.home.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Supplier Routes
    Route::prefix('supplier')->name('supplier.')->middleware('role:Supplier|Supplier Admin')->group(function () {
        // Dashboard
        Route::get('/dashboard', [App\Http\Controllers\Supplier\DashboardController::class, 'index'])->name('dashboard');
        
        // Profile Management
        Route::get('/profile', [App\Http\Controllers\Supplier\ProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/create', [App\Http\Controllers\Supplier\ProfileController::class, 'create'])->name('profile.create');
        Route::post('/profile', [App\Http\Controllers\Supplier\ProfileController::class, 'store'])->name('profile.store');
        Route::get('/profile/edit', [App\Http\Controllers\Supplier\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [App\Http\Controllers\Supplier\ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [App\Http\Controllers\Supplier\ProfileController::class, 'destroy'])->name('profile.destroy');
        
        // Test route for debugging
        Route::get('/profile/test', [App\Http\Controllers\Supplier\ProfileController::class, 'test'])->name('profile.test');
        
        // Wallet Management
        Route::get('/wallet', [App\Http\Controllers\Supplier\WalletController::class, 'index'])->name('wallet.index');
        Route::get('/wallet/transaction/{type}/{id}', [App\Http\Controllers\Supplier\WalletController::class, 'showTransaction'])->name('wallet.transaction');
        
        // Statistics
        Route::get('/statistics', [App\Http\Controllers\Supplier\StatisticsController::class, 'index'])->name('statistics.index');
        Route::get('/statistics/export', [App\Http\Controllers\Supplier\StatisticsController::class, 'export'])->name('statistics.export');
        
        // Service Management (these will be handled by existing controllers)
        Route::prefix('hotels')->name('hotels.')->group(function () {
            Route::get('/', [App\Http\Controllers\Supplier\SupplierHotelController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Supplier\SupplierHotelController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Supplier\SupplierHotelController::class, 'store'])->name('store');
            Route::get('/{hotel}/edit', [App\Http\Controllers\Supplier\SupplierHotelController::class, 'edit'])->name('edit');
            Route::put('/{hotel}', [App\Http\Controllers\Supplier\SupplierHotelController::class, 'update'])->name('update');
            Route::delete('/{hotel}', [App\Http\Controllers\Supplier\SupplierHotelController::class, 'destroy'])->name('destroy');
        });
        
        Route::prefix('tours')->name('tours.')->group(function () {
            Route::get('/', [App\Http\Controllers\Supplier\SupplierTourController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Supplier\SupplierTourController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Supplier\SupplierTourController::class, 'store'])->name('store');
            Route::get('/{tour}/edit', [App\Http\Controllers\Supplier\SupplierTourController::class, 'edit'])->name('edit');
            Route::put('/{tour}', [App\Http\Controllers\Supplier\SupplierTourController::class, 'update'])->name('update');
            Route::delete('/{tour}', [App\Http\Controllers\Supplier\SupplierTourController::class, 'destroy'])->name('destroy');
        });
        
        Route::prefix('trips')->name('trips.')->group(function () {
            Route::get('/', [App\Http\Controllers\Supplier\SupplierTripController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Supplier\SupplierTripController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Supplier\SupplierTripController::class, 'store'])->name('store');
            Route::get('/{trip}', [App\Http\Controllers\Supplier\SupplierTripController::class, 'show'])->name('show');
            Route::get('/{trip}/edit', [App\Http\Controllers\Supplier\SupplierTripController::class, 'edit'])->name('edit');
            Route::put('/{trip}', [App\Http\Controllers\Supplier\SupplierTripController::class, 'update'])->name('update');
            Route::delete('/{trip}', [App\Http\Controllers\Supplier\SupplierTripController::class, 'destroy'])->name('destroy');
        });
        
        Route::prefix('transports')->name('transports.')->group(function () {
            Route::get('/', [App\Http\Controllers\Supplier\SupplierTransportController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Supplier\SupplierTransportController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Supplier\SupplierTransportController::class, 'store'])->name('store');
            Route::get('/{transport}', [App\Http\Controllers\Supplier\SupplierTransportController::class, 'show'])->name('show');
            Route::get('/{transport}/edit', [App\Http\Controllers\Supplier\SupplierTransportController::class, 'edit'])->name('edit');
            Route::put('/{transport}', [App\Http\Controllers\Supplier\SupplierTransportController::class, 'update'])->name('update');
            Route::delete('/{transport}', [App\Http\Controllers\Supplier\SupplierTransportController::class, 'destroy'])->name('destroy');
        });
    });
});


Route::get('/test',function () {
//    $booking= \App\Models\Booking::find(53);
//    event(new NewBookingEvent($booking));
//    $start = now();
//    $blog = \App\Models\Blog::find(7);
//    $data = \App\Services\Translation\Translator::translate($blog->description, 'fr');
//    $blog->update([
//        'fr' => [
//            'description' => $data
//        ]
//    ]);
//    dd($data, now()->diffForHumans($start));
//    $c = \App\Models\CustomTrip::find(5);
//    event(new \App\Events\NewCustomTripRequestEvent($c));

//    $booking = \App\Models\Booking::latest()->first();
//    dd($booking->meta);
//    event(new \App\Events\NewBookingEvent($booking));
//    dd('SENT!');
//    $social_links = \App\Models\Setting::whereOptionKey('social_links')->first()?->option_value;
//    return view('emails.client.new-booking', compact('booking','social_links'));
});






