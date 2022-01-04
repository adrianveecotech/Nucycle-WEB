<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Helper\Helper;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Storage;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\ArticleCommand::class,
        Commands\ActivityCommand::class,
        Commands\GuidelineCommand::class,
        Commands\PromotionCommand::class,
        Commands\CollectionInactivityCommand::class,
        Commands\LevelDemoteCommand::class,
        Commands\PointExpirationCommand::class,
        Commands\RewardExpirationCommand::class,
        Commands\RewardRedemptionInactivityCommand::class,
        Commands\BinFullCommand::class,
        Commands\NotificationSchedulerCommand::class,
        Commands\MailMerchantReportCommand::class,

        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        $schedule->command('article:publish_notification')
            ->everyMinute();
        $schedule->command('activity:publish_notification')
            ->everyMinute();
        $schedule->command('guideline:publish_notification')
            ->everyMinute();
        $schedule->command('promotion:publish_notification')
            ->everyMinute();
        $schedule->command('notification_scheduler')
            ->everyMinute();
        $schedule->command('mail_merchant_report')
            ->monthlyOn(date('t'), '12:00');
        $schedule->command('point:expire_notification')
            ->dailyAt('10:00');
        // ->everyMinute();
        $schedule->command('reward_redemption:inactivity_notification')
            // ->dailyAt('11:00');
            ->weeklyOn(1, '11:00');
        // ->everyMinute();
        $schedule->command('collection:inactivity_notification')
            // ->dailyAt('12:00');
            ->weeklyOn(2, '11:00');
        $schedule->command('level:demote_notification')
            // ->dailyAt('12:00');
            ->dailyAt('11:00');
        $schedule->command('bin:full_email')
            ->dailyAt('07:00');
        $schedule->command('bin:full_email')
            ->dailyAt('15:00');
        $schedule->command('point:expire')
            ->dailyAt('00:00');
        $schedule->command('reward:expire')
            ->dailyAt('00:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }

    protected function scheduleTimezone()
    {
        return 'Asia/Kuala_Lumpur';
    }
}
