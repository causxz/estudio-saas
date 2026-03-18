<?php

namespace App\Filament\Resources\Professionals\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;

class ProfessionalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Dados do Profissional')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nome da Profissional')
                            ->required()
                            ->placeholder('Ex: Maria Silva')
                            ->maxLength(255),
                        
                        TextInput::make('phone')
                            ->label('WhatsApp (Opcional)')
                            ->tel()
                            ->maxLength(255),
                    ])->columns(1)
            ]);
    }
}