<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

class User extends Authenticatable implements FilamentUser, HasTenants
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // 1. Relação: Um utilizador pode trabalhar em vários estúdios
    public function studios(): BelongsToMany
    {
        return $this->belongsToMany(Studio::class);
    }

    // 2. Método do Filament: Quais os estúdios que este utilizador pode ver no menu?
    public function getTenants(Panel $panel): Collection
    {
        return $this->studios;
    }

    // 3. Trava de Segurança AJUSTADA: Este utilizador tem permissão para aceder a ESTE estúdio específico?
    public function canAccessTenant(Model $tenant): bool
    {
        // Verifica primeiro se o utilizador tem vínculo com o estúdio
        $belongsToStudio = $this->studios()->whereKey($tenant)->exists();
        
        if (!$belongsToStudio) {
            return false;
        }

        // Verifica a assinatura apenas deste estúdio específico
        return in_array($tenant->status, ['active', 'trialing']) || $tenant->expires_at > now();
    }

    // 4. Permissão geral para aceder ao painel AJUSTADA
    public function canAccessPanel(Panel $panel): bool
    {
        // Retorna true para permitir que o utilizador entre no sistema. 
        // A trava de segurança real agora acontece no canAccessTenant (acima) 
        // ou direciona para a criação de um novo estúdio (abaixo).
        return true;
    }

    // 5. Permissão para criar novos estúdios (tenants)
    public function canCreateTenants(): bool
    {
        // Se a usuária já tem 1 ou mais estúdios, retorna false (esconde o botão)
        // Se ela tiver 0 (acabou de criar a conta), retorna true (mostra a tela de registro)
        return $this->studios()->count() === 0;
    }
}