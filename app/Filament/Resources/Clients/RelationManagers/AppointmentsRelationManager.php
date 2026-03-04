<?php

namespace App\Filament\Resources\Clients\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class AppointmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'appointments';

    protected static ?string $title = 'Histórico de Agendamentos';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            \Filament\Forms\Components\Select::make('service_id')
                ->relationship('service', 'name')
                ->required()
                ->label('Serviço'),
            \Filament\Forms\Components\DateTimePicker::make('starts_at')
                ->required()
                ->seconds(false)
                ->displayFormat('d/m/Y H:i')
                ->label('Início'),
            \Filament\Forms\Components\DateTimePicker::make('ends_at')
                ->required()
                ->seconds(false)
                ->displayFormat('d/m/Y H:i')
                ->label('Fim'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('starts_at')
            ->columns([
                TextColumn::make('starts_at')
                    ->label('Data/Hora')
                    ->date('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('service.name')
                    ->label('Serviço'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'agendado' => 'warning',
                        'confirmado' => 'success',
                        'concluido' => 'info',
                        'cancelado' => 'danger',
                        default => 'primary',
                    })
                    ->label('Status'),
            ])
            ->headerActions([
                CreateAction::make()->label('Novo Agendamento'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([]);
    }
}