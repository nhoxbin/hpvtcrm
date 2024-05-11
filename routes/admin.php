<?php

use App\Http\Controllers\Admin\Customer\ExportController;
use App\Http\Controllers\Admin\OneBss\Export\CustomerController as ExportCustomerController;
use App\Http\Controllers\Admin\OneBss\Import\CustomerController as ImportCustomerController;
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
    Route::group(['middleware' => 'can:create,App\Models\DigiShopAccount'], function() {
        Route::apiResource('digishop', 'DigiShopController', [
        	'only' => ['create', 'store']
        ]);
    });

    // OneBss
    Route::group(['as' => 'onebss.', 'prefix' => 'onebss', 'namespace' => 'OneBss', 'middleware' => 'can:create,App\Models\OneBssAccount'], function() {
        Route::get('login', 'OAuthController@create')->name('create');
        Route::post('login', 'OAuthController@login')->name('login');
        Route::post('oauth', 'OAuthController@oauth')->name('oauth');
        Route::post('customers/import', ImportCustomerController::class)->name('customers.import');
        Route::get('customers/export', ExportCustomerController::class)->name('customers.export');

        Route::apiResource('customers', 'CustomerController', [
        	'only' => ['index', 'update', 'show']
        ]);
        Route::delete('customers/{customer?}', 'CustomerController@destroy')->name('customers.destroy');
    });
});
