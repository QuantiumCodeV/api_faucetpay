<?php

namespace App\Console;

use App\Jobs\DailyInterestJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // Запланируйте выполнение задания DailyInterestJob раз в минуту для тестирования
        $schedule->job(new DailyInterestJob)->everyMinute();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        // Ваша команда artisan
        require base_path('routes/console.php');
    }
}
