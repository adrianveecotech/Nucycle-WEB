<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactUsEnquiry extends Model
{
    use HasFactory;

    protected $table = 'contact_us_enquiry';

    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'message',
    ];
}
