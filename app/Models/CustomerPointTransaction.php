<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerPointTransaction extends Model
{
    use HasFactory;

    protected $table = 'customer_point_transaction';

    protected $fillable = [
        'customer_id',
        'point',
        'balance',
        'description',
        'value',
        'expiration_date',
        'status',
    ];

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer');
    }
}
