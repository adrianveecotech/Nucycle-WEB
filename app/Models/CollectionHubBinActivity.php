<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollectionHubBinActivity extends Model
{
    use HasFactory;

    protected $table = 'collection_hub_bin_activity';

    protected $fillable = [
        'description',
    ];
}
