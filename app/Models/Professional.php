<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Professional extends Model
{
    use SoftDeletes;
    
    protected $fillable = ['studio_id', 'name', 'phone'];

    public function studio(): BelongsTo
    {
        return $this->belongsTo(Studio::class);
    }
}