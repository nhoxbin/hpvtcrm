<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::group([
	'as' => 'admin.',
	'prefix' => 'admin',
	// 'namespace' => 'Admin',
], function() {
    // Auth::routes(['register' => false]);

    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
});
    
Route::group([
	'as' => 'admin.',
	'prefix' => 'admin',
	'namespace' => 'Admin',
	'middleware' => ['auth']
], function() {
	// Nhân viên
    Route::resource('users', 'UserController', [
    	'only' => ['index', 'store', 'edit', 'update', 'destroy']
    ]);

    Route::get('about', 'AboutController@index')->name('about');
    Route::post('customers', 'MultipleCustomersController@store')->name('customers.store');
    Route::post('customers/export', 'MultipleCustomersController@export')->name('customers.export');

    Route::group(['middleware' => 'admin'], function() {
        // delete multiple customer
        Route::post('customers/delete', 'MultipleCustomersController@destroy')->name('customers.destroy');

        // customer
        Route::resource('customer', 'CustomerController', [
        	'only' => ['destroy']
        ]);
    });
});
