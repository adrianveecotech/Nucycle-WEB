<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecycleType extends Model
{
    use HasFactory;

    protected $table = 'recycle_type';

    protected $fillable = [
        'name',
        'recycle_category_id',
        'created_at',
        'updated_at',
    ];

    public function recycle_category()
    {
        return $this->belongsTo('App\Models\RecycleCategory');
    }

    


}

