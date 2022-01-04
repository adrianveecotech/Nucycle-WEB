<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BannerTag extends Model
{
    use HasFactory;

    protected $table = 'banner_tag';

    protected $fillable = [
        'name',
        'is_active',
        'created_at',
        'updated_at'
    ];
}
