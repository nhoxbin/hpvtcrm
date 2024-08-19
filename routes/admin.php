<?php

use App\Http\Controllers\Admin\Customer\ExportController;
use App\Http\Controllers\Admin\DigiShop\Import\CustomerController as ImportDigiShopCustomerController;
use App\Http\Controllers\Admin\DigiShop\Export\CustomerController as ExportDigiShopCustomerController;
use App\Http\Controllers\Admin\OneBss\Import\CustomerController as ImportOneBssCustomerController;
use App\Http\Controllers\Admin\OneBss\Export\CustomerController as ExportOneBssCustomerController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::group([
	'as' => 'admin.',
	'prefix' => 'admin',
	'namespace' => 'Admin',
	'middleware' => ['auth', 'role:Super Admin|Admin']
], function() {
    Route::get('/', 'DashboardController@index')->name('dashboard');
    Route::get('dashboard', 'DashboardController@index')->name('dashboard');
    Route::get('about', 'AboutController@index')->name('about');

	// Nhân viên
    Route::apiResource('users', 'UserController', [
    	'only' => ['index', 'store', 'edit', 'update', 'destroy']
    ]);

	// Khách hàng
    Route::get('customers/export', ExportController::class)->name('customers.export');
    Route::apiResource('customers', 'CustomerController', [
        'except' => ['destroy', 'show']
    ]);
    Route::delete('customers/{customer?}', 'CustomerController@destroy')->name('customers.destroy');

    // Route::post('customers', 'MultipleCustomersController@store')->name('customers.store');

    Route::group(['middleware' => 'admin'], function() {
        // delete multiple customer
        // Route::post('customers/delete', 'MultipleCustomersController@destroy')->name('customers.destroy');

        // customer
        /* Route::resource('customer', 'CustomerController', [
        	'only' => ['destroy']
        ]); */
    });

    // DigiShop
    Route::group(['as' => 'digishop.', 'prefix' => 'digishop', 'namespace' => 'DigiShop', 'middleware' => 'can:create,App\Models\DigiShopAccount'], function() {
        Route::apiResource('accounts', 'AccountController', [
        	'only' => ['create', 'store']
        ]);

        Route::post('customers/import', ImportDigiShopCustomerController::class)->name('customers.import');
        Route::get('customers/export', ExportDigiShopCustomerController::class)->name('customers.export');
        Route::apiResource('customers', 'CustomerController', [
        	'only' => ['index', 'create', 'store']
        ]);

    });

    // OneBss
    Route::group(['as' => 'onebss.', 'prefix' => 'onebss', 'namespace' => 'OneBss', 'middleware' => 'role:OneBss Admin'], function() {
        Route::post('login', 'OAuthController@login')->name('accounts.login');
        Route::post('oauth', 'OAuthController@oauth')->name('accounts.oauth');
        Route::apiResource('accounts', 'OAuthController', [
        	'only' => ['index', 'destroy']
        ]);
        Route::post('customers/import', ImportOneBssCustomerController::class)->name('customers.import');
        Route::get('customers/export', ExportOneBssCustomerController::class)->name('customers.export');

        Route::put('customers/distribute', 'DistributeController')->name('customers.distribute');
        Route::delete('customers/{customer?}', 'CustomerController@destroy')->name('customers.destroy');
        Route::apiResource('customers', 'CustomerController', [
        	'only' => ['index', 'update', 'show']
        ]);
        Route::apiResource('users', 'UserController', [
            'only' => ['index', 'destroy']
        ]);
    });
});
