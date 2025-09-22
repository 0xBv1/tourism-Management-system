<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RecaptchaController;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\PasswordController;
use App\Http\Controllers\Api\Payment\PaypalController;
use App\Http\Controllers\Api\Payment\FawaterkController;
use App\Http\Controllers\Api\CalendarController;

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
    
    /*Auth Middleware*/
    Route::group(['middleware' => 'auth:client'], function () {
        Route::group(['prefix' => 'profile', 'as' => 'profile'], function () {
            Route::patch('/', [ProfileController::class, 'update'])->name('update');
            Route::get('me', [ProfileController::class, 'me'])->name('me');
            Route::post('change/image', [ProfileController::class, 'changeProfileImage'])->name('change.profile.image');
            Route::post('logout', [ProfileController::class, 'logout'])->name('logout');
        });
    });
    
    /*Auth Middleware*/
    Route::group(['prefix' => 'auth', 'as' => 'auth.'], function () {
        Route::post('/login', [AuthController::class, 'login'])->name('login');
        Route::post('/register', [AuthController::class, 'register'])->name('register');
        Route::post('password/forget', [PasswordController::class, 'forget'])->name('client.forget.password');
        Route::post('password/reset', [PasswordController::class, 'reset'])->name('client.reset.password');
        Route::post('password/otp/verify', [PasswordController::class, 'otpVerify'])->name('client.password.otp.verify');
    });
    
    Route::group(['prefix' => 'countries', 'as' => 'countries.'], function () {
        Route::get('/', [CountryController::class, 'index'])->name('index');
        Route::get('/{id}', [CountryController::class, 'show'])->name('show');
    });
    
    Route::get('settings', [SettingController::class, 'index'])->name('settings');
    
    // Calendar API
    Route::get('calendar/availability', [CalendarController::class, 'getAvailability'])->name('calendar.availability');
    
    //RoutePlace
});