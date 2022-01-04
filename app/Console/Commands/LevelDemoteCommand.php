<?php

namespace App\Console\Commands;

use App\Helper\Helper;
use App\Models\Customer;
use App\Models\Level;
use Illuminate\Console\Command;

class LevelDemoteCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'level:demote_notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notification to remind user of level demotion after 2 months of inactive in collection. ';

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
        foreach ($customers as $value) {
            if (count($value->collection) != 0) {
                $latest_collect = date_create(date('Y-m-d', strtotime($value->collection->max('created_at')->toDateTimeString())));
                $today = (date_create(date('Y-m-d')));
                $diff = (date_diff($today, $latest_collect));
                if ($diff->days == 60) {
                    $points_from = ($value->membership->level->points_from);
                    $previousLevel = Level::where('points_to', '>=', $points_from - 5)->where('points_from', '<=', $points_from - 5)->first();
                    $value->membership->level_id = $previousLevel->id;
                    $value->membership->points = $previousLevel->points_to;
                    $value->membership->save();

                    $title = 'Level demoted';
                    $body = 'Due to inactive collection activity in the previous 2 months, you have been demoted to the lower level, ' . $previousLevel->name;
                    $notification_data = array("detail" => '');
                    $user_type = 'customer';

                    if ($value->user->device_token != '' && $value->user->receive_notification == 1 && $value->user->device_token != NULL) {
                        Helper::configNotification($value->user->device_token, $value->id, $title, $body, $notification_data, $user_type);
                    } else {
                        Helper::configNotification('', $value->id, $title, $body, $notification_data, $user_type);
                    }
                }
            }
        }
    }
}
