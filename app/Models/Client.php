<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'whatsapp', 'birth_date', 'preferences_summary'];

    public function anamneses()
    {
        return $this->hasMany(Anamnesis::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    // INTELIGÊNCIA DO ALERTA DE RETORNO
    public function getReturnAlertAttribute()
    {
        $futureAppointment = $this->appointments()
            ->where('starts_at', '>', now())
            ->where('status', '!=', 'cancelado')
            ->first();

        if ($futureAppointment) {
            return '✅ Retorno Agendado: ' . \Carbon\Carbon::parse($futureAppointment->starts_at)->format('d/m/Y');
        }

        $lastAppointment = $this->appointments()
            ->where('starts_at', '<', now())
            ->where('status', 'concluido')
            ->orderBy('starts_at', 'desc')
            ->first();

        if ($lastAppointment) {
            $lastDate = \Carbon\Carbon::parse($lastAppointment->starts_at)->startOfDay();
            $today = now()->startOfDay();

            $days = $lastDate->diffInDays($today);

            $textoDia = $days <= 1 ? 'dia' : 'dias';

            return "⏳ Último atendimento há {$days} {$textoDia}";
        }


        return 'Nenhum atendimento concluído ainda.';
    }

    // Relação SaaS: Este registro pertence a um Estúdio
    public function studio(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Studio::class);
    }
}
