<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AttackController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('', [UserController::class, 'index'])->name('index');
        
        Route::middleware(['verifyAdmin'])->group(function () {
            Route::get('/ulti/player', [UserController::class, 'ultiPlayer'])->name('ultiPlayer');
            Route::post('', [UserController::class, 'store'])->name('store');
            Route::put('{id}', [UserController::class, 'update'])->name('update');
            Route::delete('{id}', [UserController::class, 'destroy'])->name('destroy');
        });

        Route::get('inventory', [UserController::class, 'inventory'])->name('inventory');
        Route::get('equipment', [UserController::class, 'equipment'])->name('equipment');
    });

    Route::prefix('items')->name('items.')->group(function () {
        Route::get('', [ItemController::class, 'index'])->name('index');

        Route::middleware(['verifyAdmin'])->group(function () {
            Route::post('', [ItemController::class, 'store'])->name('store');
            Route::put('{id}', [ItemController::class, 'update'])->name('update');
            Route::delete('{id}', [ItemController::class, 'destroy'])->name('destroy');
        });

        Route::put('{id}/buy', [ItemController::class, 'buyItem'])->name('buyItem');
        Route::put('{id}/equip', [ItemController::class, 'equipItem'])->name('equipItem');
    });

    Route::prefix('attacks')->name('attacks.')->group(function () {
        Route::post('', [AttackController::class, 'store'])->name('store');
    });
});
