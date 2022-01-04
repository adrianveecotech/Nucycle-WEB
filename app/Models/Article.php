<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $table = 'article';

    protected $fillable = [
        'title',
        'image',
        'description',
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
}
