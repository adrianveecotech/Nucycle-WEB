<?php

namespace App\Console\Commands;

use App\Helper\Helper;
use App\Models\Customer;
use App\Models\Level;
use Illuminate\Console\Command;

class CollectionInactivityCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'collection:inactivity_notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notification if customer does not make any collection actvitiy for the past 1 month.';

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
            if (count($value->collection) == 0) {
                array_push($user, $value->id);
                if ($value->user->device_token != '' && $value->user->receive_notification == 1 && $value->user->device_token != NULL ) {
                    array_push($user_token, $value->user->device_token);
                }
            } else {
                // $latest_collect = date_create($value->collection->max('created_at'));
                $latest_collect = date_create(date('Y-m-d', strtotime($value->collection->max('created_at')->toDateTimeString())));
                $today = (date_create(date('Y-m-d')));
                $diff = (date_diff($today, $latest_collect));
                if ($diff->days > 30) {
                    array_push($user, $value->id);
                    if ($value->user->device_token != '' && $value->user->receive_notification == 1 && $value->user->device_token != NULL) {
                        array_push($user_token, $value->user->device_token);
                    }
                }
            }
        }
        $title = 'No collection activity recently';
        $body = 'You have not make any collection activity for the past 1 month. You will be demoted to previous level after 2 months of inactivity.';
        $notification_data = array("detail" => '');
        $user_type = 'customer';
        Helper::configNotification($user_token, $user, $title, $body, $notification_data, $user_type);
    }
}
