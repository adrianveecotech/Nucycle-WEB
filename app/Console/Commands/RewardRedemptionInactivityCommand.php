<?php

namespace App\Console\Commands;

use App\Helper\Helper;
use App\Models\Customer;
use Illuminate\Console\Command;

class RewardRedemptionInactivityCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reward_redemption:inactivity_notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notification if customer does not make any reward redemption for the past 2 months.';

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
        $customers = Customer::get();
        $user = array();
        $user_token = array();
        foreach ($customers as $value) {
            if (count($value->reward) == 0) {
                array_push($user, $value->id);
                if ($value->user->device_token != '' && $value->user->receive_notification == 1 && $value->user->device_token != NULL) {
                    array_push($user_token, $value->user->device_token);
                }
            } else {
                // $latest_redeem = date_create($value->reward->max('redeem_date'));
                $latest_redeem = date_create(date('Y-m-d', strtotime($value->reward->max('redeem_date')->toDateTimeString())));
                $today = (date_create(date('Y-m-d')));
                $diff = (date_diff($today, $latest_redeem));
                if ($diff->days > 60) {
                    array_push($user, $value->id);
                    if ($value->user->device_token != '' && $value->user->receive_notification == 1) {
                        array_push($user_token, $value->user->device_token);
                    }
                }
            }
        }
        $title = 'No reward redemption activity recently';
        $body = 'You have not make any reward redemption for the past 2 months.';
        $notification_data = array("detail" => '');
        $user_type = 'customer';
        Helper::configNotification($user_token, $user, $title, $body, $notification_data, $user_type);
    }
}
