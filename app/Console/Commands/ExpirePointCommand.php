<?php

namespace App\Console\Commands;

use App\Helper\Helper;
use Illuminate\Console\Command;
use App\Models\Collection;
use App\Models\CustomerMembership;
use App\Models\CustomerPointTransaction;
use App\Models\Level;
use App\Models\Reward;

class ExpirePointCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'point:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire';

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
        $date = date('Y-m-d');
        $expirePoint = CustomerPointTransaction::whereDate('expiration_date', '<', $date)->where('status', 1)->where('balance', '>', 0)->get()->toArray();
        foreach ($expirePoint as $value) {
            CustomerPointTransaction::create([
                'customer_id' => $value['customer_id'],
                'point' => -$value['balance'],
                'description' => 'expired',
                'value' => $value['id'],
            ]);

            $expiringPoint = CustomerPointTransaction::find($value['id']);
            $expiringPoint->status = 0;
            $expiringPoint->save();

            $membership =  CustomerMembership::where('customer_id', $value['customer_id'])->first();
            $membership->points = round($membership->points - $value['balance'], 2);
            $level = Level::where('points_from', '<=', $membership->points)->where('points_to', '>=', $membership->points)->first();
            $membership->level_id = $level->id;
            $membership->save();
        }
    }
}
