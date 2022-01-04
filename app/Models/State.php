<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;
    protected $table = 'state';

    public function collection_hub()
    {
        return $this->hasMany('App\Models\CollectionHub');
    }
}
