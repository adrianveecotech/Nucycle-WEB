<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Collector extends Model
{
    use HasFactory;

    protected $table = 'collector';

    protected $fillable = [
        'id',
        'user_id',
        'name',
        'email',
        'phone',
        'profile_picture',
        'collection_hub_id',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function collection_hub()
    {
        return $this->belongsTo('App\Models\CollectionHub');
    }
}
