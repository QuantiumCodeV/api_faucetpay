<?php

namespace App\Jobs;

use App\Models\UserBalance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Transactions;
use App\Models\Currency;
class DailyInterestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
       // Получаем все балансы пользователей
       $userBalances = UserBalance::all();
        
       foreach ($userBalances as $userBalance) {
           // Начисляем 10% к балансу
           $interest = $userBalance->balance * env('DAILY_INTEREST_RATE', 0.10);
           $userBalance->balance += $interest;
           $userBalance->save();

           $transaction = new Transactions();
           $transaction->user_id = $userBalance->user_id;
           $currency = Currency::where('ticker', $userBalance->currency->ticker)->first();
           $transaction->coin = $currency->ticker;
           $transaction->faucet_name = 'Daily Interest';
           $transaction->amount = $interest;
           $transaction->referral_payment = 0;
           $transaction->date = time();
           $transaction->save();
       }
    }
}
