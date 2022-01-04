<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notification';

    protected $fillable = [
        'message',
        'title',
        'data',
        'user_type',
        'status',
        'time_set',
        'time_sent'
    ];

}
