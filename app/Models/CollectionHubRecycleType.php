<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollectionHubRecycleType extends Model
{
    use HasFactory;

    protected $table = 'collection_hub_recycle';

    protected $fillable = [
        'recycle_type_id',
        'collection_hub_id',
        'point',
        'start_date',
        'end_date',
        'is_active',
        'created_at',
        'updated_at'
    ];

    public function collection_hub()
    {
        return $this->belongsTo('App\Models\CollectionHub');
    }

    public function recycle_type()
    {
        return $this->belongsTo('App\Models\RecycleType');
    }
}
