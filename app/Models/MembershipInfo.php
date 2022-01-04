<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipInfo extends Model
{
    use HasFactory;

    protected $table = 'membership_info';

    protected $fillable = [
       'content'
    ];
}
