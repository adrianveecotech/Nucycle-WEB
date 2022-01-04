<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeOurPartnerContent extends Model
{
    use HasFactory;

    protected $table = 'be_our_partner_content';

    protected $fillable = [
        'content',
        'type_id',
    ];

    public function be_our_partner_type()
    {
        return $this->belongsTo('App\Models\BeOurPartnerType','type_id');

    }
}
