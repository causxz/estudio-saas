<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Studio extends Model
{
    use HasFactory;

    // Apenas campos que os utilizadores podem preencher diretamente
    protected $fillable = [
        'name',
        'slug',
        'has_commissions',
    ];

    // Proteção extra: impede explicitamente a atribuição em massa destes campos sensíveis
    protected $guarded = [
        'id',
        'asaas_customer_id',
        'subscription_id',
        'plan_type',
        'status',
        'expires_at'
    ];

    protected $casts = [
        'has_commissions' => 'boolean',
    ];

    // --- RELAÇÕES --- //

    // Relação: Um estúdio tem muitos utilizadores
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    // As gavetas do Estúdio (Relações HasMany)
    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }
    
    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function anamneses(): HasMany
    {
        return $this->hasMany(Anamnesis::class);
    }

    public function professionals(): HasMany
    {
        return $this->hasMany(Professional::class);
    }
}