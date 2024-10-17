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

        foreach ($currencies as $currency) {
            Log::info($currency->tickers);
            Log::info(json_decode($currency->tickers)[0]);
            Log::info($addresses);
            #Log::info($addresses[json_decode($currency->tickers)[0]]);
            Log::info("===================");
            UserBalance::create([
                'user_id' => $user->id,
                'currency_id' => $currency->id,
                'balance' => 0,
                'address' => $addresses[json_decode($currency->tickers)[0]],
            ]);
           # Log::info($addresses[json_decode($currency->tickers)[0]]);
            Log::info("===================");
        }
    }
}