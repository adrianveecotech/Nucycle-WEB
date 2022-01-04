<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Merchant extends Model
{
    use HasFactory;

    protected $table = 'merchant';

    protected $fillable = [
        'name',
        'address',
        'phone_number',
        'postcode',
        'state_id',
        'city_id',
        'email',
        'url',
        'is_active',
        'basic_report',
        'ads_report',
        'subscription_report',
        'created_at',
        'updated_at'
    ];

    public function state()
    {
        return $this->belongsTo('App\Models\State');
    }

    public function city()
    {
        return $this->belongsTo('App\Models\City');
    }
}
