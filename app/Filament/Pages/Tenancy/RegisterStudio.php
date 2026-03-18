<?php

namespace App\Filament\Pages\Tenancy;

use App\Models\Studio;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Pages\Tenancy\RegisterTenant;
use Illuminate\Support\Str;
use Filament\Forms\Components\Toggle;

class RegisterStudio extends RegisterTenant
{
    public static function getLabel(): string
    {
        return 'Cadastrar Meu Estúdio';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nome do Estúdio')
                    ->placeholder('Ex: Louyse Lash Design')
                    ->required()
                    ->maxLength(255),
                    
                // O Toggle agora está no lugar certo, dentro dos componentes!
                Toggle::make('has_commissions')
                    ->label('Ativar Sistema de Comissões')
                    ->helperText('Ligue se o seu estúdio possui profissionais que ganham por comissão.')
                    ->default(false),
            ]);
    }

    protected function handleRegistration(array $data): Studio
    {
        // 1. Cria o slug automaticamente a partir do nome
        $data['slug'] = Str::slug($data['name']);

        // 2. Cria o estúdio no banco de dados
        $studio = Studio::create($data);

        // 3. Vincula o estúdio à usuária que está a fazer o cadastro (Acesso ao painel)
        $studio->users()->attach(auth()->user());

        // Cria a dona do estúdio como a primeira "Profissional" automaticamente!
        $studio->professionals()->create([
            'name' => auth()->user()->name,
        ]);

        return $studio;
    }
}