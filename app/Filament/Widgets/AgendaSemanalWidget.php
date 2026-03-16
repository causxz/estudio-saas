<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Appointment;
use Carbon\Carbon;

class AgendaSemanalWidget extends BaseWidget
{
    protected static ?int $sort = 2; // Fica na posição 2 (abaixo dos cartões financeiros)
    protected int | string | array $columnSpan = 'full'; 
    protected static ?string $heading = 'Controle de Agenda';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Appointment::query()->orderBy('starts_at', 'asc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('starts_at')
                    ->label('Data e Hora')
                    ->dateTime('d/m/Y H:i')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('client.name')
                    ->label('Cliente')
                    ->searchable(),
                Tables\Columns\TextColumn::make('service.name')
                    ->label('Serviço'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'agendado' => 'warning',
                        'confirmado' => 'success',
                        'concluido' => 'info',
                        'cancelado' => 'danger',
                        default => 'gray'
                    }),
            ])
            ->filters([
                Filter::make('periodo')
                    ->form([
                        Select::make('valor')
                            ->label('Mostrar agendamentos de:')
                            ->options([
                                'hoje' => 'Hoje',
                                'amanha' => 'Amanhã',
                                'semana' => 'Próximos 7 Dias',
                                'todos' => 'Todos os Horários',
                            ])
                            ->default('hoje')
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['valor'] ?? 'hoje') {
                            'hoje' => $query->whereDate('starts_at', Carbon::today()),
                            'amanha' => $query->whereDate('starts_at', Carbon::tomorrow()),
                            'semana' => $query->whereBetween('starts_at', [Carbon::today(), Carbon::today()->addDays(7)]),
                            default => $query,
                        };
                    })
            ]);
    }
}