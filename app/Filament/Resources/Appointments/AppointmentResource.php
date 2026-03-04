<?php

namespace App\Filament\Resources\Appointments;

use App\Filament\Resources\Appointments\Pages;
use App\Models\Appointment;
use App\Models\Service;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Carbon\Carbon;

// Componentes do Formulário v5
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

// Componentes da Tabela v5
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $modelLabel = 'Agendamento';
    protected static ?string $pluralModelLabel = 'Agendamentos';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detalhes do Agendamento')
                    ->columns(2)
                    ->schema([
                        Select::make('client_id')
                            ->relationship('client', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Cliente'),

                        Select::make('service_id')
                            ->relationship('service', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Serviço')
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                $startsAt = $get('starts_at');
                                if ($state && $startsAt) {
                                    $service = Service::find($state);
                                    if ($service) {
                                        $set('ends_at', Carbon::parse($startsAt)->addMinutes($service->duration_minutes)->toDateTimeString());
                                    }
                                }
                            }),

                        DateTimePicker::make('starts_at')
                            ->required()
                            ->label('Data e Hora de Início')
                            ->seconds(false) // Oculta os segundos no relógio
                            ->displayFormat('d/m/Y H:i') // Mostra no padrão BR para o usuário
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                $serviceId = $get('service_id');
                                if ($state && $serviceId) {
                                    $service = \App\Models\Service::find($serviceId);
                                    if ($service) {
                                        $set('ends_at', \Carbon\Carbon::parse($state)->addMinutes($service->duration_minutes)->toDateTimeString());
                                    }
                                }
                            }),

                        DateTimePicker::make('ends_at')
                            ->required()
                            ->label('Data e Hora de Término')
                            ->seconds(false) // Oculta os segundos no relógio
                            ->displayFormat('d/m/Y H:i') // Mostra no padrão BR
                            ->helperText('Calculado automaticamente com base na duração do serviço.')
                            ->rules([
                                function (Get $get) {
                                    return function (string $attribute, $value, $fail) use ($get) {
                                        $startsAt = $get('starts_at');
                                        $endsAt = $value;
                                        $appointmentId = $get('id');

                                        $exists = \App\Models\Appointment::where(function ($query) use ($startsAt, $endsAt) {
                                            $query->whereBetween('starts_at', [$startsAt, $endsAt])
                                                ->orWhereBetween('ends_at', [$startsAt, $endsAt])
                                                ->orWhere(function ($q) use ($startsAt, $endsAt) {
                                                    $q->where('starts_at', '<=', $startsAt)
                                                        ->where('ends_at', '>=', $endsAt);
                                                });
                                        })  
                                            ->where('status', '!=', 'cancelado')
                                            ->when($appointmentId, fn($q) => $q->where('id', '!=', $appointmentId))
                                            ->exists();

                                        if ($exists) {
                                            $fail('Este horário já está ocupado por outro agendamento.');
                                        }
                                    };
                                },
                            ]),

                        Select::make('status')
                            ->options([
                                'agendado' => 'Agendado',
                                'confirmado' => 'Confirmado',
                                'concluido' => 'Concluído',
                                'cancelado' => 'Cancelado',
                            ])
                            ->required()
                            ->default('agendado')
                            ->label('Status do Atendimento'),

                        Textarea::make('notes')
                            ->label('Observações')
                            ->columnSpanFull(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('client.name')
                    ->searchable()
                    ->sortable()
                    ->label('Cliente'),

                TextColumn::make('service.name')
                    ->label('Serviço'),

                TextColumn::make('starts_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->label('Início'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'agendado' => 'warning',
                        'confirmado' => 'success',
                        'concluido' => 'info',
                        'cancelado' => 'danger',
                        default => 'primary',
                    })
                    ->label('Status'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
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
