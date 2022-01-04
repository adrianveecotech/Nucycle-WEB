<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Reward extends Model
{
    use HasFactory;

    protected $table = 'rewards';

    protected $fillable = [
        'merchant_id',
        'reward_category_id',
        'title',
        'image',
        'point',
        'description',
        'tag',
        'start_date',
        'end_date',
        'terms',
        'status',
        'created_at',
        'updated_at',
    ];

    public function merchant()
    {
        return $this->belongsTo('App\Models\Merchant');
    }

    public function reward_category()
    {
        return $this->belongsTo('App\Models\RewardsCategory');
    }

    public function reward_tag()
    {
        return $this->hasMany('App\Models\RewardTag');
    }
}
