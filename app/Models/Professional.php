<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Professional extends Model
{
    protected $fillable = ['studio_id', 'name', 'phone'];

    public function studio(): BelongsTo
    {
        return $this->belongsTo(Studio::class);
    }
}