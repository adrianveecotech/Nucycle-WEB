<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MerchantReportMailActivity extends Model
{
    use HasFactory;

    protected $table = 'merchant_report_mail_activity';

    protected $fillable = [
        'merchant_id',
        'message'
    ];
}
