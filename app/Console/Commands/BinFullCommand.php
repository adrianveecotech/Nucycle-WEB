<?php

namespace App\Console\Commands;

use App\Helper\Helper;
use App\Models\Activity;
use App\Models\Notification;
use App\Models\NotificationRecipient;
use App\Models\User;
use App\Models\ContactUsInfo;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BinFullCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bin:full_email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email when bin indicator is full.';

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
        $bin = DB::select("SELECT *,chb.id as id from collection_hub_bin chb LEFT JOIN recycle_type rt on chb.recycle_type_id = rt.id LEFT JOIN collection_hub ch on chb.collection_hub_id = ch.id where current_weight/capacity_weight >= 1 ");

        $message = '';
        foreach ($bin as  $value) {
            $message .= "Bin " . $value->name . " at " . $value->hub_name . " has exceeded 100%. \n";
        }
        $subject = "Collection hub bin full";

		$info = ContactUsInfo::first();
        Helper::sendEmail($info->email, $subject, $message);
    }
}
