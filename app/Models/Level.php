<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    use HasFactory;

    protected $table = 'level';

    protected $fillable = [
      'name',
      'description',
      'multiplier',
      'points_from',
      'points_to',
      'free_monthly_voucher',
      'image',
    ];
}
