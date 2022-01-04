<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatisticIndicator extends Model
{
    use HasFactory;

    protected $table = 'statistic_indicator';

    protected $fillable = [
        'name',
        'created_at',
        'updated_at'
    ];
}
