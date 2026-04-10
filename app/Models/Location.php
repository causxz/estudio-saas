<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use SoftDeletes;
    protected $fillable = ['studio_id', 'name', 'address', 'maps_link'];

    public function studio()
    {
        return $this->belongsTo(Studio::class);
    }
}
