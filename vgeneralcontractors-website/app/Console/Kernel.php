<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * These cron jobs are run in the background by a cron service.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('sitemap:generate')->daily();
        
        // Send appointment reminders for tomorrow's inspections at 9 AM Central Time
        $schedule->command('app:send-appointment-reminders')
                 ->dailyAt('09:00')
                 ->timezone('America/Chicago') // Central Time (Texas/Chicago)
                 ->appendOutputTo(storage_path('logs/appointment-reminders.log'));
                 
        // Verificar nuevas llamadas de Retell API cada 15 minutos
        $schedule->command('retell:check-calls')
                 ->everyFifteenMinutes()
                 ->appendOutputTo(storage_path('logs/retell-calls.log'));

        // Publish scheduled posts every minute
        $schedule->command('app:publish-scheduled-posts')
                 ->everyMinute()
                 ->appendOutputTo(storage_path('logs/scheduled-posts.log'));
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\CheckNewCalls::class,
        Commands\GenerateSitemap::class,
        Commands\PublishScheduledPosts::class,
        Commands\DiagnoseInvoicePdfs::class,
    ];
} 