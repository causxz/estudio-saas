<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'client_id',
        'service_id',
        'professional_id',
        'starts_at',
        'ends_at',
        'status',
        'notes',
        'location_id',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class)->withTrashed();
    }

    public function service()
    {
        return $this->belongsTo(Service::class)->withTrashed();
    }


    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }

    public function studio(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Studio::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    // ADICIONADO o withTrashed()
    public function professional()
    {
        return $this->belongsTo(Professional::class, 'professional_id')->withTrashed();
    }
}
