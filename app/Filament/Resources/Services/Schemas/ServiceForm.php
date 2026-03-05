<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nome do Serviço')
                    ->required(),

                TextInput::make('price')
                    ->label('Preço')
                    ->required()
                    ->numeric()
                    ->prefix('R$'),

                TextInput::make('duration_minutes')
                    ->label('Duração (minutos)')
                    ->required()
                    ->numeric(),

                TextInput::make('buffer_after')
                    ->label('Tempo de Limpeza / Margem (minutos)')
                    ->helperText('Tempo extra bloqueado na agenda após o procedimento.')
                    ->numeric()
                    ->default(10) // Sugere 10 min por padrão
                    ->required(),
            ]);
    }
}