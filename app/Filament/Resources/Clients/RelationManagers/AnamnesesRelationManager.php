<?php

namespace App\Filament\Resources\Clients\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
// AQUI ESTÁ A CORREÇÃO DA ROTA:
use Filament\Actions\Action; 

class AnamnesesRelationManager extends RelationManager
{
    protected static string $relationship = 'anamneses';

    protected static ?string $title = 'Histórico de Fichas (Anamneses)';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('created_at')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Data do Procedimento')
                    ->date('d/m/Y H:i')
                    ->sortable(),
                    
                TextColumn::make('preferred_style')
                    ->label('Estilo Realizado')
                    ->searchable(),
                    
                TextColumn::make('observations')
                    ->label('Observações')
                    ->limit(50),
            ])
            ->recordActions([
                Action::make('ver_ficha')
                    ->label('Ver Ficha')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('primary')
                    ->url(fn ($record) => \App\Filament\Resources\Anamneses\AnamnesisResource::getUrl('edit', ['record' => $record->id]))
            ])
            ->toolbarActions([]);
    }
}