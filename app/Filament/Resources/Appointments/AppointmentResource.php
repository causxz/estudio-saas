<?php

namespace App\Filament\Resources\Appointments;

use Filament\Support\Icons\Heroicon;
use App\Filament\Resources\Appointments\Pages;
use App\Models\Appointment;
use App\Models\Service;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Carbon\Carbon;

use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\Indicator;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Facades\Filament;
use App\Filament\Resources\Appointments\Schemas\AppointmentForm;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;
    protected static ?string $modelLabel = 'Agendamento';
    protected static ?string $pluralModelLabel = 'Agendamentos';
    protected static ?string $navigationLabel = 'Agendamentos';
    
    protected static ?string $tenantOwnershipRelationshipName = 'studio';

    public static function form(Schema $schema): Schema
    {
        return AppointmentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('client.name')->label('Cliente')->searchable(),
                TextColumn::make('service.name')->label('Serviço'),
                
                // A COLUNA DO PROFISSIONAL - DEVE ESTAR AQUI NAS COLUMNS DA TABELA
                TextColumn::make('professional.name')
                    ->label('Profissional')
                    ->searchable()
                    ->sortable()
                    ->badge() 
                    ->color('gray')
                    ->visible(fn() => Filament::getTenant() && Filament::getTenant()->has_commissions),

                TextColumn::make('starts_at')->label('Início')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('status')->badge()->color(fn($state) => match ($state) {
                    'agendado' => 'warning',
                    'concluido' => 'success',
                    'cancelado' => 'danger',
                    default => 'gray'
                }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Filtrar por Status')
                    ->options([
                        'agendado' => '📅 Agendado',
                        'concluido' => '✅ Concluído',
                        'cancelado' => '❌ Cancelado',
                    ])
                    ->multiple()
                    ->preload(),

                Filter::make('hoje')
                    ->label('Agendamentos de Hoje')
                    ->toggle()
                    ->query(fn(Builder $query): Builder => $query->whereDate('starts_at', Carbon::today()))
                    ->indicator('Hoje'),

                Filter::make('data_agendamento')
                    ->form([
                        DatePicker::make('agendado_de')->label('Agendado de:'),
                        DatePicker::make('agendado_ate')->label('Agendado até:'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['agendado_de'],
                                fn(Builder $query, $date): Builder => $query->whereDate('starts_at', '>=', $date),
                            )
                            ->when(
                                $data['agendado_ate'],
                                fn(Builder $query, $date): Builder => $query->whereDate('starts_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['agendado_de'] ?? null) {
                            $indicators[] = Indicator::make('A partir de ' . Carbon::parse($data['agendado_de'])->format('d/m/Y'))
                                ->removeField('agendado_de');
                        }
                        if ($data['agendado_ate'] ?? null) {
                            $indicators[] = Indicator::make('Até ' . Carbon::parse($data['agendado_ate'])->format('d/m/Y'))
                                ->removeField('agendado_ate');
                        }
                        return $indicators;
                    })
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->defaultSort('starts_at', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAppointments::route('/'),
            'create' => Pages\CreateAppointment::route('/create'),
            'edit' => Pages\EditAppointment::route('/{record}/edit'),
        ];
    }
}