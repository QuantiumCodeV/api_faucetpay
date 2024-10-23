<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserBalanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\TransactionsController;
// Маршрут для регистрации аккаунта


Route::group(['prefix' => 'account'], function () {
    Route::post('/register', [UserController::class, 'create']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
    Route::middleware('auth:sanctum')->post('/get-user-information', [UserController::class, 'getUserInformation']);
    Route::middleware('auth:sanctum')->get('/get-2fa-details', [UserController::class, 'get2FADetails']);
    Route::middleware('auth:sanctum')->post('/change-email', [UserController::class, 'changeEmail']);
    Route::middleware('auth:sanctum')->get('/get-socket-token', [UserController::class, 'getSocketToken']);
    Route::middleware('auth:sanctum')->post('/change-password', [UserController::class, 'changePassword']);
});

Route::get('/test', function () {
    return response()->json(['message' => 'Hello, World!']);
});

Route::get('/coins/get-all', [CurrencyController::class, 'getCurrencies']);

//Группа маршрутов, требующих аутентификации
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/coins/get-balances', [UserController::class, 'getBalances']);
    Route::get('/games/has-agreed', [GameController::class, 'hasAgreed']);
    Route::get('/games/can-access', [GameController::class, 'canAccess']);
});

Route::post('/westwallet/ipn', [UserBalanceController::class, 'ipn'])->name('westwallet.ipn');

Route::middleware('auth:sanctum')->post('/rp/claim-daily-rp', [UserController::class, 'claimDailyRewardPoints']);
Route::middleware('auth:sanctum')->get('/wallet/get-information', [UserController::class, 'getInformation']);

Route::post('/westwallet/create-address', [UserController::class, 'createAddress']);
Route::post('/westwallet/create-addresses', [UserController::class, 'createAddresses']);

Route::middleware('auth:sanctum')->post('/wallet/get-transactions', [TransactionsController::class, 'getAll']);