<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollectionHubAdmin extends Model
{
    use HasFactory;

    protected $table = 'hub_admin';

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'collection_hub_id'
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
