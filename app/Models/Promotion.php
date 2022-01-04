<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    protected $table = 'promotion';

    protected $fillable = [
        'title',
        'image',
        'description',
        'merchant_id',
        'banner_tag_id',
        'start_date',
        'end_date',
        'status',
        'created_at',
        'updated_at',
    ];

    public function banner_tag()
    {
        return $this->belongsTo('App\Models\BannerTag');
    }

    public function merchant()
    {
        return $this->belongsTo('App\Models\Merchant');
    }
}
