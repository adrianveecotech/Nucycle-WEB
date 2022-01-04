<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecycleCategory extends Model
{
    use HasFactory;

    protected $table = 'recycle_category';

    protected $fillable = [
        'name',
        'cars_removed',
        'household_saving',
        'water_saved',
        'wheelie_bins',
        'created_at',
        'updated_at',
    ];

    public function recycle_type()
    {
        return $this->hasMany('App\Models\RecycleType');
    }

    public function recycle_category_statistic_indicator()
    {
        return $this->hasMany('App\Models\RecycleCategoryStatisticIndicator');
    }
}
