<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Topic extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    public function contents(): BelongsToMany
    {
        return $this->belongsToMany(Content::class);
    }
}
