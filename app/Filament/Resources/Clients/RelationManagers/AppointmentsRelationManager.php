<?php

namespace App\Filament\Resources\Clients\RelationManagers;

use App\Models\Service;
use Carbon\Carbon;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

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
                ->label('Serviço')
                ->live()
                ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                    $startsAt = $get('starts_at');
                    if ($state && $startsAt) {
                        $service = Service::find($state);
                        if ($service) {
                            $totalMinutes = $service->duration_minutes + ($service->buffer_after ?? 0);
                            $set('ends_at', Carbon::parse($startsAt)->addMinutes($totalMinutes)->toDateTimeString());
                        }
                    }
                }),

            \Filament\Forms\Components\DateTimePicker::make('starts_at')
                ->required()
                ->seconds(false)
                ->displayFormat('d/m/Y H:i')
                ->label('Início')
                ->live()
                ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                    $serviceId = $get('service_id');
                    if ($state && $serviceId) {
                        $service = Service::find($serviceId);
                        if ($service) {
                            $totalMinutes = $service->duration_minutes + ($service->buffer_after ?? 0);
                            $set('ends_at', Carbon::parse($state)->addMinutes($totalMinutes)->toDateTimeString());
                        }
                    }
                }),

            \Filament\Forms\Components\DateTimePicker::make('ends_at')
                ->required()
                ->seconds(false)
                ->displayFormat('d/m/Y H:i')
                ->label('Fim (Com Buffer)'),

            \Filament\Forms\Components\Select::make('status')
                ->options([
                    'agendado' => 'Agendado',
                    'confirmado' => 'Confirmado',
                    'concluido' => 'Concluído',
                    'cancelado' => 'Cancelado',
                ])
                ->default('agendado')
                ->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('starts_at')
            ->columns([
                TextColumn::make('starts_at')
                    ->label('Data/Hora')
                    ->dateTime('d/m/Y H:i')
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