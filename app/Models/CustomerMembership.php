<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerMembership extends Model
{
    use HasFactory;

    protected $table = 'customer_membership';

    protected $fillable = [
      'customer_id',
      'level_id',
      'points',
    ];

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer');
    }

    public function level()
    {
        return $this->belongsTo('App\Models\Level');
    }
}
