<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerReward extends Model
{
    use HasFactory;

    protected $table = 'customer_reward';

    protected $fillable = [
        'customer_id',
        'reward_id',
        'redeem_date',
        'voucher_code',
        'point_used',
        'created_at',
        'updated_at',
    ];

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer');
    }

    public function reward()
    {
        return $this->hasOne('App\Models\Reward');
    }
}
