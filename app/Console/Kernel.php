<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Register custom commands here
     */
    protected $commands = [
        \App\Console\Commands\AutoCompleteOrders::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        Log::info('ENV: '.env('APP_ENV'));
        Log::info('DB Connection: '.env('DB_CONNECTION'));
        Log::info('DB Name: '.env('DB_DATABASE')); // Thêm dòng này

        Log::info('✅ schedule:run đã chạy lúc '.now());

        $schedule->command('app:auto-complete-orders')->everyMinute()
            ->before(function () {
                Log::info('Bắt đầu chạy schedule');
            })
            ->after(function () {
                Log::info('Hoàn thành chạy schedule');
            });
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
