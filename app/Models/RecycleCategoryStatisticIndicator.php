<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecycleCategoryStatisticIndicator extends Model
{
    use HasFactory;

    protected $table = 'recycle_category_statistic_indicator';

    protected $fillable = [
        'id',
        'recycle_category_id',
        'name',
        'indicator_id',
        'value',
    ];

    public function recycle_category()
    {
        return $this->belongsTo('App\Models\RecycleCategory');
    }

    public function statistic_indicator()
    {
        return $this->belongsTo('App\Models\StatisticIndicator','indicator_id');
    }


}
