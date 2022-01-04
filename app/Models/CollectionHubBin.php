<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollectionHubBin extends Model
{
    use HasFactory;

    protected $table = 'collection_hub_bin';

    protected $fillable = [
        'recycle_type_id',
        'collection_hub_id',
        'capacity_weight',
        'current_weight',
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
