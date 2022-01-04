<?php

namespace App\Console\Commands;

use App\Helper\Helper;
use Illuminate\Console\Command;
use App\Models\Collection;
use App\Models\CustomerMembership;
use App\Models\CustomerPointTransaction;
use App\Models\Reward;

class PointExpirationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'point:expire_notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notification when point is expiring';

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
        $points = CustomerPointTransaction::get();
        foreach ($points as $point) {
            $notification_data = array("detail" => '');
            $expiration_date = (date_create($point->expiration_date));
            $today = (date_create(date('Y-m-d')));
            $diff = (date_diff($expiration_date, $today));
            if ($diff->days == 60) {
                $user_token = ($point->customer->user->device_token);
                $user = ($point->customer->user->id);
                $title =  'Point expires in 2 months';
                $body =  $point->all_point . ' points will be expired on ' . $point->expiration_date . '. Kindly use it before it expires.';
                $user_type = 'customer';
                if ($point->customer->user->device_token != '' && $point->customer->user->receive_notification == 1 && $point->customer->user->device_token != NULL)
                    Helper::configNotification($user_token, $user, $title, $body, $notification_data, $user_type);
                else
                    Helper::configNotification('', $user, $title, $body, $notification_data, $user_type);
            }
            if ($diff->days == 15) {
                $user_token = ($point->customer->user->device_token);
                $user = ($point->customer->user->id);
                $title =  'Point expires in 15 days';
                if ((CustomerMembership::where('customer_id', $point->customer->id))->first()->points < Reward::min('point')) {
                    $body =  "Your total points do not meet the lowest reward redemption. Please go to the low coins redemption rewards page.";
                } else {
                    $body =  $point->all_point . ' points will be expired on ' . $point->expiration_date . '. Kindly use it before it expires.';
                    $notification_data = array("detail" => 'low_coin_redemption');
                }
                $user_type = 'customer';
                if ($point->customer->user->device_token != '' && $point->customer->user->receive_notification == 1 && $point->customer->user->device_token != NULL)
                    Helper::configNotification($user_token, $user, $title, $body, $notification_data, $user_type);
                else
                    Helper::configNotification('', $user, $title, $body, $notification_data, $user_type);
            }
        }
    }
}
