<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    protected $table = 'voucher';

    protected $fillable = [
        'reward_id',
        'code',
        'expiry_date',
        'is_redeem',
        'created_at',
        'updated_at'
    ];

    public function redeemed_by()
    {
        return $this->hasOne('App\Models\CustomerReward','voucher_id');
    }
}
