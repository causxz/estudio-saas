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
        return $this->belongsTo(Client::class);
    }

    // Novo relacionamento para puxar os dados do serviço
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    // Um agendamento pode ter uma transação financeira (O pagamento dele)
    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }

    // Relação SaaS: Este registro pertence a um Estúdio
    public function studio(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Studio::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function professional()
    {
        return $this->belongsTo(Professional::class, 'professional_id');
    }

    
}