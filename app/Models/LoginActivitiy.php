<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginActivitiy extends Model
{
    use HasFactory;

    protected $table = 'login_activity';

    protected $fillable = [
        'user_id',
    ];
}
