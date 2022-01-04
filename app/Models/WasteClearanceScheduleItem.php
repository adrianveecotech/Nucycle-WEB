<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WasteClearanceScheduleItem extends Model
{
    use HasFactory;

    protected $table = 'waste_clearance_schedule_item';

    protected $fillable = [
        'waste_clearance_schedule_id',
        'recycle_type_id',
        'weight',
        'created_at',
        'updated_at',
    ];

    public function waste_clearance_schedule()
    {
        return $this->belongsTo('App\Models\WasteClearanceSchedule');
    }

    public function recycle_type()
    {
        return $this->belongsTo('App\Models\RecycleType');
    }
}
