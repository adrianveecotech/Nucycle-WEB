<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeOurPartnerType extends Model
{
    use HasFactory;

    protected $table = 'be_our_partner_type';

    protected $fillable = [
        'name',
    ];
}
