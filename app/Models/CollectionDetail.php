<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollectionDetail extends Model
{
    use HasFactory;

    protected $table = 'collection_detail';

    protected $fillable = [
        'collection_id',
        'recycling_type_id',
        'weight',
        'total_point',
        'created_at',
        'updated_at',
    ];

    public function collection()
    {
        return $this->belongsTo('App\Models\Collection');
    }

    public function recycle_type()
    {
        return $this->belongsTo('App\Models\RecycleType','recycling_type_id');
    }



}
