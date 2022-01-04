<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersRoles extends Model
{
    use HasFactory;

    protected $table = 'user_role';

    protected $fillable = [
        'user_id',
        'role_id'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function role()
    {
        return $this->belongsTo('App\Models\UserRole', 'role_id');
    }
}
