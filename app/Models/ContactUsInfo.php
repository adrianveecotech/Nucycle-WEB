<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactUsInfo extends Model
{
    use HasFactory;

    protected $table = 'contact_us_information';

    protected $fillable = [
        'facebook_url',
        'instagram_url',
        'website_url',
        'email',
        'phone',    
        'address'

    ];

}
