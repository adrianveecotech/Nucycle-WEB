<?php

namespace App\Console\Commands;

use App\Helper\Helper;
use App\Models\Guideline;
use App\Models\Notification;
use App\Models\NotificationRecipient;
use App\Models\User;
use Illuminate\Console\Command;

class GuidelineCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'guideline:publish_notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notification when guideline is published.';

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
        })->where('user_role.role_id', 2)->pluck('users.id')->all();

        $guidelines = (Guideline::get());
        foreach ($guidelines as $guideline) {
            if (strtotime(date('Y-m-d H:i')) == strtotime(date('Y-m-d H:i', strtotime($guideline->start_date)))) {
                $title = 'New Guideline';
                $body = $guideline->title;
                $notification_data = array("detail" => 'guideline', "id" => $guideline->id);
                $user_type = 'customer';

                Helper::configNotification($user_token, $user, $title, $body, $notification_data, $user_type);
                // $notification = array(
                //     'to' => $user_token,
                //     // 'sound' => 'default',
                //     'title' =>  'New Guideline',
                //     'body' =>   $guideline->title,
                // );
                // Helper::sendNotification($notification);
                // $notification_data = array("detail" => 'guideline', "id" => $guideline->id);
                // $notification_id = Notification::create([
                //     'title' =>  'New Guideline',
                //     'message' => $guideline->title,
                //     'user_type' => 'customer',
                //     'data' => json_encode($notification_data)
                // ])->id;
                // foreach ($user as $value) {
                //     NotificationRecipient::create([
                //         'user_id' =>  $value,
                //         'notification_id' => $notification_id
                //     ]);
                // };
            }
        }
    }
}
