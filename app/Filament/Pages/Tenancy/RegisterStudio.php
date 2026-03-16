<?php

namespace App\Filament\Pages\Tenancy;

use App\Models\Studio;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Pages\Tenancy\RegisterTenant;
use Illuminate\Support\Str;

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
            ]);
    }

    protected function handleRegistration(array $data): Studio
    {
        // 1. Cria o slug automaticamente a partir do nome
        $data['slug'] = Str::slug($data['name']);

        // 2. Cria o estúdio no banco de dados
        $studio = Studio::create($data);

        // 3. Vincula o estúdio à usuária que está a fazer o cadastro.
        $studio->users()->attach(auth()->user());

        return $studio;
    }
}