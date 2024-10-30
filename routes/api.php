<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserBalanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\TransactionsController;
use App\Http\Controllers\MonthlyStatisticController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\LinkAdressesController;
use App\Http\Controllers\WithdrawController;
// Маршрут для регистрации аккаунта


Route::group(['prefix' => 'account'], function () {
    Route::post('/register', [UserController::class, 'create']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/get-user-information', [UserController::class, 'getUserInformation']);
        Route::get('/get-2fa-details', [UserController::class, 'get2FADetails']);
        Route::post('/change-email', [UserController::class, 'changeEmail']);
        Route::get('/get-socket-token', [UserController::class, 'getSocketToken']);
        Route::post('/change-password', [UserController::class, 'changePassword']);
    });
});

Route::get('/test', function () {
    return response()->json(['message' => 'Hello, World!']);
});

Route::get('/coins/get-all', [CurrencyController::class, 'getCurrencies']);

//Группа маршрутов, требующих аутентификации
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/coins/get-balances', [UserController::class, 'getBalances']);
    Route::post('/rp/claim-daily-rp', [UserController::class, 'claimDailyRewardPoints']);
    Route::get('/wallet/get-information', [UserController::class, 'getInformation']);
    Route::post('/wallet/get-transactions', [TransactionsController::class, 'getAll']);
    Route::post('/wallet/get-monthly-statistics', [MonthlyStatisticController::class, 'getMonthlyStatistics']);
    Route::post('/wallet/get-deposit-history', [DepositController::class, 'getDepositHistory']);
    Route::get('/wallet/get-linked-addresses', [LinkAdressesController::class, 'getLinkedAddresses']);
    Route::post('/wallet/link-address', [LinkAdressesController::class, 'linkAddress']);
    Route::post('/wallet/remove-linked-address', [LinkAdressesController::class, 'removeLinkedAddress']);

    Route::get('/wager-mining/get-information', [UserController::class, 'getWagerMiningInformation']);
    Route::post('/wallet/get-deposit-address', [UserController::class, 'getDepositAddress']);
    Route::get('/wallet/ask-for-review', [WithdrawController::class, 'askForReview']);
    Route::get('/wallet/get-withdrawal-information', [WithdrawController::class, 'getWithdrawalInformation']);
});



Route::get('/games/can-access', [GameController::class, 'canAccess']);
Route::get('/games/has-agreed', [GameController::class, 'hasAgreed']);

Route::post('/westwallet/ipn', [UserBalanceController::class, 'ipn'])->name('westwallet.ipn');

Route::post('/westwallet/create-address', [UserController::class, 'createAddress']);
Route::post('/westwallet/create-addresses', [UserController::class, 'createAddresses']);
