<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'appointment_id',
        'description',
        'type',
        'amount',
        'payment_method',
        'transaction_date',
        'notes',
    ];

    // Transforma a data do banco em um objeto Carbon automaticamente
    protected $casts = [
        'transaction_date' => 'date',
    ];

    // RELACIONAMENTO: Uma transação pode pertencer a um Agendamento
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    // Relação SaaS: Este registro pertence a um Estúdio
    public function studio(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Studio::class);
    }
}