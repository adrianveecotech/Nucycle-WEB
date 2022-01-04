<?php

namespace App\Console\Commands;

use App\Models\Reward;
use Illuminate\Console\Command;

class RewardExpirationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reward:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change reward status to expired';

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
        $rewards = Reward::get();
        foreach ($rewards as $value) {
            if ($value->end_date < date('Y-m-d')) {
                $value->status = 2;
                $value->save();
            }
        }
    }
}
