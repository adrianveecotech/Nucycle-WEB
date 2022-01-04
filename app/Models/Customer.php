<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customer';

    protected $fillable = [
        'id',
        'user_id',
        'name',
        'email',
        'phone',
        'profile_picture',
        'address',
        'postcode',
        'city',
        'state',
        'bank_account_name',
        'bank_account_bank',
        'bank_account_number',
        'touchngo_phone_number',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function City()
    {
        return $this->belongsTo('App\Models\City', 'city');
    }

    public function State()
    {
        return $this->belongsTo('App\Models\State', 'state');
    }

    public function bank()
    {
        return $this->hasOne('App\Models\Bank', 'bank_account_bank');
    }

    public function reward()
    {
        return $this->hasMany('App\Models\CustomerReward');
    }

    public function collection()
    {
        return $this->hasMany('App\Models\Collection');
    }

    public function membership()
    {
        return $this->hasOne('App\Models\CustomerMembership');
    }
}
