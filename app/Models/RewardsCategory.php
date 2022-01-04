<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RewardsCategory extends Model
{
    use HasFactory;

    protected $table = 'reward_category';

    protected $fillable = [
        'name',
        'created_at',
        'updated_at'
    ];
}
