<?php

namespace App\Filament\Pages\Tenancy;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Pages\Tenancy\EditTenantProfile;

class EditStudioProfile extends EditTenantProfile
{
    public static function getLabel(): string
    {
        return 'Perfil do Estúdio';
    }

    public function form(Schema $schema): Schema
    {
        return $schema 
            ->components([
                TextInput::make('name')
                    ->label('Nome do Estúdio')
                    ->required()
                    ->maxLength(255),

                Toggle::make('has_commissions')
                    ->label('Ativar Sistema de Comissões')
                    ->default(false),
            ]);
    }
}