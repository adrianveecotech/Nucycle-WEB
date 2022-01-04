<?php

namespace App\Console\Commands;

use App\Helper\Helper;
use App\Models\Article;
use App\Models\Notification;
use App\Models\NotificationRecipient;
use App\Models\User;
use Illuminate\Console\Command;

class NotificationSchedulerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification_scheduler';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run notification scheduler.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $notifications = Notification::where('status', 'draft')->where('time_set', '<=', date('Y-m-d H:i:s'))->get();
        foreach ($notifications as $notification) {
            $notification->when = "now_from_scheduler";
            Helper::webSendNotification($notification);
        }
    }
}
