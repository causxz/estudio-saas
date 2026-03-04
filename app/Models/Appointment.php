<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'client_id', 
        'service_id', 
        'starts_at',  
        'ends_at',    
        'status', 
        'notes'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // Novo relacionamento para puxar os dados do serviço
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}