<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollectionHubCollector extends Model
{
    use HasFactory;

    protected $table = 'collector';

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'profile_picture',
        'collection_hub_id'
    ];
}
