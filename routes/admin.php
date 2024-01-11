<?php

use App\Http\Controllers\Admin\Customer\ExportController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
 
Route::group([
	'as' => 'admin.',
	'prefix' => 'admin',
	'namespace' => 'Admin',
	'middleware' => ['auth']
], function() {
    Route::get('dashboard', 'DashboardController@index')->name('dashboard');
    Route::get('about', 'AboutController@index')->name('about');

	// Nhân viên
    Route::resource('users', 'UserController', [
    	'only' => ['index', 'store', 'edit', 'update', 'destroy']
    ]);

	// Khách hàng
    Route::resource('customers', 'CustomerController', [
    	'only' => ['index', 'store', 'edit', 'update', 'destroy']
    ]);

    // Route::post('customers', 'MultipleCustomersController@store')->name('customers.store');
    Route::post('customers/export', ExportController::class)->name('customers.export');

    Route::group(['middleware' => 'admin'], function() {
        // delete multiple customer
        // Route::post('customers/delete', 'MultipleCustomersController@destroy')->name('customers.destroy');

        // customer
        Route::resource('customer', 'CustomerController', [
        	'only' => ['destroy']
        ]);
    });
});
