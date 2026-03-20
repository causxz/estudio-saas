<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'appointment_id',
        'professional_id',
        'studio_id',
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

    // RELACIONAMENTO: Uma transação pode pertencer a um Profissional (opcional)
    public function professional()
    {
        return $this->belongsTo(Professional::class);
    }

    protected static function booted()
    {
        static::created(function (Transaction $transaction) {
            // 1. Só dispara se for uma "entrada" e estiver vinculada a um agendamento
            if ($transaction->type === 'entrada' && $transaction->appointment_id) {

                // Verifica se o estúdio tem o módulo de comissões LIGADO
                $studio = \App\Models\Studio::find($transaction->studio_id);
                if (!$studio || !$studio->has_commissions) {
                    return; // Aborta a automação se a chave estiver desligada
                }

                // 2. Busca o agendamento completo com o serviço
                $appointment = \App\Models\Appointment::with('service')->find($transaction->appointment_id);

                // 3. Verifica se existe um profissional, serviço e se a comissão é > 0
                if ($appointment && $appointment->professional_id && $appointment->service && $appointment->service->commission_amount > 0) {

                    // 4. Trava de segurança contra duplicidade
                    $jaExiste = \App\Models\Transaction::where('appointment_id', $transaction->appointment_id)
                        ->where('type', 'saida')
                        ->where('professional_id', $appointment->professional_id)
                        ->exists();

                    if (!$jaExiste) {
                        // 5. Cria a saída automaticamente no financeiro
                        \App\Models\Transaction::create([
                            'studio_id' => $transaction->studio_id,
                            'appointment_id' => $transaction->appointment_id,
                            'professional_id' => $appointment->professional_id,
                            'type' => 'saida',
                            'amount' => ($appointment->service->price * $appointment->service->commission_percentage) / 100,
                            'description' => 'Comissão automática - ' . $appointment->service->name,
                            'transaction_date' => $transaction->transaction_date,
                            'payment_method' => null,
                            'notes' => 'Gerado automaticamente pelo sistema através da entrada #' . $transaction->id,
                        ]);
                    }
                }
            }
        });
    }
}
