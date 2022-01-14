<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WasteClearanceSchedulePayment extends Model
{
    use HasFactory;

    protected $table = 'waste_clearance_schedule_payment';

    protected $fillable = [
        'invoice_date',
        'unit_price',
        'total_price',
        'receipt_date',
        'receipt_number',
        'total_amount',
        'image',
        'waste_clearance_schedule_id'
    ];
}
