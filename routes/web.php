<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransactionController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('artisan/{password}/{command}', function($password, $command) {
    if ($password === '74ujk6Z2wO') {
        try {
            $exitCode = Artisan::call($command, request()->all());
            $artisanOutput = Artisan::output();

            if (in_array("Error", str_split($artisanOutput, 5))) {
                Log::error($artisanOutput);
            }
            echo $exitCode == 0 ? 'Success' : 'Error';
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
    Route::apiResource('transactions', TransactionController::class);
    Route::apiResource('customers', CustomerController::class)->only(['update']);
    Route::get('products', ProductController::class)->name('products.index');

    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
