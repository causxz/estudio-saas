<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Appointment;
use Carbon\Carbon;

class AgendamentosHojeWidget extends BaseWidget
{
    // Define a ordem: 2 significa que vai ficar logo abaixo dos cartões numéricos
    protected static ?int $sort = 2; 
    
    // Faz a tabela ocupar a largura inteira da tela
    protected int | string | array $columnSpan = 'full'; 
    
    // Título da tabela
    protected static ?string $heading = 'Agenda do Dia';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                // Busca apenas os agendamentos de hoje, ordenados pelo horário mais cedo
                Appointment::query()
                    ->whereDate('starts_at', Carbon::today())
                    ->orderBy('starts_at', 'asc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('starts_at')
                    ->label('Horário')
                    ->dateTime('H:i') // Mostra apenas a hora (Ex: 14:30)
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
            ->paginated(false); // Retira a paginação para mostrar todos do dia numa lista só
    }
}