<?php

namespace App\Jobs;

use App\Models\UserBalance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
       }
    }
}
