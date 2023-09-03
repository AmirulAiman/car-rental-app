<?php

use App\Http\Controllers\AppController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Models\Car;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', [AppController::class,'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');

Route::resource('cars', CarController::class)->only(['index','show']);
Route::middleware('auth')->group(function () {
    Route::resource('users', UserController::class); #User
    Route::resource('cars', CarController::class)->only(['create','store','update','edit','destroy']); #Car
    Route::prefix('cars')->group(function() {
        Route::get('rent/{car}', [CarController::class,'rent'])->name('cars.rent');
        Route::post('booking', [CarController::class,'book'])->name('cars.book');
        Route::post('payment/{booking}', [CarController::class,'payment'])->name('cars.payment');
    });

    Route::post('/respond/{booking}',[AppController::class,'respond'])->name('app.respond');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
