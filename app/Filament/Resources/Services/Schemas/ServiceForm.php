<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Facades\Filament;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detalhes do Serviço')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->label('Nome do Serviço')
                            ->maxLength(255),

                        TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('R$')
                            ->label('Preço do Serviço'),

                        TextInput::make('duration_minutes')
                            ->required()
                            ->numeric()
                            ->label('Duração (Minutos)')
                            ->helperText('Tempo real de execução do atendimento.'),

                        TextInput::make('buffer_after')
                            ->numeric()
                            ->label('Tempo de Limpeza (Minutos)')
                            ->helperText('Tempo extra bloqueado na agenda após o serviço.')
                            ->default(0),

                        // --- COMISSÃO ---
                        TextInput::make('commission_percentage')
                            ->label('Comissão do Profissional (%)')
                            ->numeric()
                            ->suffix('%')
                            ->placeholder('Ex: 40')
                            ->minValue(0)
                            ->maxValue(100)
                            // Só aparece se a chave "Comissões" do Estúdio estiver LIGADA
                            ->visible(fn () => Filament::getTenant() && Filament::getTenant()->has_commissions)
                            ->helperText('Porcentagem que o profissional recebe ao realizar este serviço.'),


                        Textarea::make('description')
                            ->label('Descrição')
                            ->columnSpanFull(),
                    ])
            ]);
    }
}