<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransactionController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('artisan/{password}/{command}', function($password, $command) {
    if ($password === '74ujk6Z2wO') {
        $exitCode = Artisan::call($command, request()->all());
        $artisanOutput = Artisan::output();

        if (in_array("Error", str_split($artisanOutput, 5))) {
            throw new Exception($artisanOutput);
        }
        echo $exitCode == 0 ? 'Success' : 'Error';
    }
});

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware('auth')->group(function () {
	Route::get('dashboard', 'DashboardController@index')->name('dashboard');

    Route::get('about', fn () => Inertia::render('About'))->name('about');

    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::apiResource('transactions', TransactionController::class);

    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
