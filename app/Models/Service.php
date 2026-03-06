<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['name', 'price', 'duration_minutes', 'buffer_after'];

    // Relação SaaS: Este registro pertence a um Estúdio
    public function studio(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Studio::class);
    }
}

