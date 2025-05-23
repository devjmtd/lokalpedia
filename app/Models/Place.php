<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'latitude',
        'longitude',
    ];
}
