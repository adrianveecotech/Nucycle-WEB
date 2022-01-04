<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WasteClearanceSchedule extends Model
{
    use HasFactory;

    protected $table = 'waste_clearance_schedule';

    protected $fillable = [
        'collection_time',
        'collection_hub_id',
        'buyer_name',
        'buyer_phone_number',
        'pin_code',
        'status',
        'created_at',
        'updated_at',
    ];

    public function collection_hub()
    {
        return $this->belongsTo('App\Models\CollectionHub');
    }

    public function items(){
        return $this->hasMany('App\Models\WasteClearanceScheduleItem','waste_clearance_schedule_id');
    }

    public function itemsCollected(){
        return $this->hasMany('App\Models\WasteClearanceItem','waste_clearance_schedule_id');
    }
}
