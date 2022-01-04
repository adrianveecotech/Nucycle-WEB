<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationRecipient extends Model
{
    use HasFactory;

    protected $table = 'notification_recipient';

    protected $fillable = [
        'message',
        'user_id',
        'read_at',
        'notification_id'
    ];
}
