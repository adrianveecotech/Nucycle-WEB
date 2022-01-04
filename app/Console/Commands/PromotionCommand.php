<?php

namespace App\Console\Commands;

use App\Helper\Helper;
use App\Models\Promotion;
use App\Models\Notification;
use App\Models\NotificationRecipient;
use App\Models\User;
use Illuminate\Console\Command;

class PromotionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'promotion:publish_notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notification when promotion is published.';

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
        $user_token = User::leftJoin('user_role', function ($join) {
            $join->on('users.id', '=', 'user_role.user_id');
        })->where('device_token', '!=', null)->where('device_token', '!=', '')->where('user_role.role_id', 2)->where('receive_notification', 1)->pluck('device_token')->all();
        $user = User::leftJoin('user_role', function ($join) {
            $join->on('users.id', '=', 'user_role.user_id');
        })->where('user_role.role_id', 2)->pluck('id')->all();

        $promotions = (Promotion::get());
        foreach ($promotions as $promotion) {
            if (strtotime(date('Y-m-d H:i')) == strtotime(date('Y-m-d H:i', strtotime($promotion->start_date)))) {
                $title = 'New Promotion';
                $body = $promotion->title;
                $notification_data = array("detail" => 'promotion', "id" => $promotion->id);
                $user_type = 'customer';

                Helper::configNotification($user_token, $user, $title, $body, $notification_data, $user_type);
            }
        }
    }
}
