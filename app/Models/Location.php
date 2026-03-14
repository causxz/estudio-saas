<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = ['studio_id', 'name', 'address', 'maps_link'];

    public function studio()
    {
        return $this->belongsTo(Studio::class);
    }
}
