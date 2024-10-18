<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Currency;
use App\Models\UserBalance;
use App\Services\WestWalletService;
use Illuminate\Support\Facades\Log;

class GenerateUserWallets implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function handle()
    {
        $user = User::findOrFail($this->userId);
        $westWalletService = new WestWalletService();
        $currencies = Currency::all();
        $addresses = $westWalletService->generateAllAdresses($user->id);
        Log::info("===ПОЛУЧЕНИЕ АДРЕСОВ===");
        Log::info($addresses);
        Log::info("===ПОЛУЧЕНИЕ_КУРСНЫХ_АДРЕСОВ===");
        foreach ($currencies as $currency) {
            Log::info($currency->tickers);
            Log::info(json_decode($currency->tickers)[0]);
            Log::info($addresses);
            #Log::info($addresses[json_decode($currency->tickers)[0]]);
            Log::info("===================");
            $userBalance = new UserBalance();
            $userBalance->user_id = $user->id;
            $userBalance->currency_id = $currency->id;
            $userBalance->balance = 0;
            $userBalance->address = $addresses[json_decode($currency->tickers)[0]];
            $userBalance->save();

           # Log::info($addresses[json_decode($currency->tickers)[0]]);
            Log::info("===================");
        }
    }
}