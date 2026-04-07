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

    // 3. Trava de Segurança: Este utilizador tem permissão para aceder a ESTE estúdio específico?
    public function canAccessTenant(Model $tenant): bool
    {
        return $this->studios()->whereKey($tenant)->exists();
    }

    // 4. Permissão geral para aceder ao painel
    public function canAccessPanel(Panel $panel): bool
    {
        $studio = $this->studios()->first();

        if (!$studio) return false;

        // Se estiver ativo ou ainda no trial, permite acesso
        return in_array($studio->status, ['active', 'trialing']) || $studio->expires_at > now();
    }
}
