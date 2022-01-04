<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeOurPartnerEnquiry extends Model
{
    use HasFactory;

    protected $table = 'be_our_partner_enquiry';

    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'message',
        'type_id',
    ];

    public function be_our_partner_type()
    {
        return $this->belongsTo('App\Models\BeOurPartnerType', 'type_id');
    }
}
