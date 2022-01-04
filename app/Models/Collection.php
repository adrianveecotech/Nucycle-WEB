<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    use HasFactory;

    protected $table = 'collection';

    protected $fillable = [
        'customer_id',
        'collection_hub_id',
        'collector_id',
        'total_point',
        'bonus_point',
        'all_point',
        'total_weight',
        'photo',
        'created_at',
        'updated_at',
    ];

    public function collection_hub()
    {
        return $this->belongsTo('App\Models\CollectionHub');
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer');
    }

    public function collector()
    {
        return $this->belongsTo('App\Models\Collector');
    }

    public function collection_detail()
    {
        return $this->hasMany('App\Models\CollectionDetail');
    }
}
