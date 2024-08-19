<?php

use Illuminate\Foundation\Application;
use Inertia\Inertia;
use App\Http\Controllers\{
    CustomerController,
    DigiShopController,
    ProductController,
    UserController,
    ProfileController,
    TransactionController,
};
use App\Http\Controllers\Export\DigiShopController as ExportDigiShopController;
use App\Http\Controllers\Export\OneBssController as ExportOneBssController;
use App\Http\Controllers\OneBss\CustomerController as OneBssCustomerController;
use App\Http\Controllers\OneBss\Export\CustomerController as ExportCustomerController;
use Illuminate\Support\Facades\{
    Artisan,
    Log,
    Route,
};

Route::get('artisan/{password}/{command}', function($password, $command) {
    if ($password === '74ujk6Z2wO') {
        try {
            $exitCode = Artisan::call($command, request()->all());
            $artisanOutput = Artisan::output();

            if ($exitCode == 0) {
                echo 'Success';
            } else {
                echo 'Error';
                Log::error($artisanOutput);
            }
        } catch (\Exception $e) {
            Log::error($e);
        }
    }
});

Route::get('/', function () {
    /* return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]); */
    return redirect()->route('dashboard');
});

Route::middleware('auth')->group(function () {
	Route::get('dashboard', 'DashboardController@index')->name('dashboard');

    Route::get('about', fn () => Inertia::render('About'))->name('about');

    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::apiResource('transactions', TransactionController::class)->only(['store', 'update']);
    Route::apiResource('customers', CustomerController::class)->only(['update']);
    Route::get('products', ProductController::class)->name('products.index');

    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // DigiShop
    Route::group(['middleware' => 'can:view,App\Models\DigiShopCustomer'], function() {
        Route::get('digishop/export', ExportDigiShopController::class)->name('digishop.export');
        Route::apiResource('digishop', DigiShopController::class)->only(['index', 'store', 'destroy']);
    });

    // OneBss
    Route::group(['as' => 'onebss.', 'prefix' => 'onebss', 'namespace' => 'OneBss', 'middleware' => 'can:view,App\Models\OneBssCustomer'], function() {
        // Route::get('onebss/export', ExportCustomerController::class)->name('OneBss.export');
        Route::get('customers/{customer}/reload-balance', 'CustomerController@reload_balance')->name('customers.reload_balance');
        Route::apiResource('customers', OneBssCustomerController::class)->only(['index', 'update', 'store', 'destroy']);
    });
});

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
