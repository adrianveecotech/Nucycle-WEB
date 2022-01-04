<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollectionHub extends Model
{
    protected $table = 'collection_hub';

    protected $fillable = [
        'hub_name',
        'hub_address',
        'hub_postcode',
        'hub_state_id',
        'hub_city_id',
        'contact_number',
        'operating_hours',
        'is_active',
        'type',
        'image',
        'operating_day',
        'longitude',
        'latitude',
        'read_only'
    ];

    use HasFactory;

    public function collection_hub_recycle_type()
    {
        return $this->hasMany('App\Models\CollectionHubRecycleType');
    }

    public function state()
    {
        return $this->belongsTo('App\Models\State','hub_state_id');
    }  
    
    public function city()
    {
        return $this->belongsTo('App\Models\City','hub_city_id');
    }   
}
