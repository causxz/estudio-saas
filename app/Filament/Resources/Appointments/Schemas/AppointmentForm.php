<?php

namespace App\Filament\Resources\Appointments\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Facades\Filament;
use App\Models\Service;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Professional;

class AppointmentForm
{
    public static function configure(Schema $schema): Schema
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
                                        $totalMinutes = $service->duration_minutes + ($service->buffer_after ?? 0);
                                        $set('ends_at', Carbon::parse($startsAt)->addMinutes($totalMinutes)->toDateTimeString());
                                    }
                                }
                            }),

                        // --- CAMPO COMISSÃO ---
                        Select::make('professional_id')
                            ->relationship(
                                name: 'professional',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn(Builder $query) => $query->where('studio_id', Filament::getTenant()->id)
                            )
                            ->label('Profissional Responsável')
                            ->searchable()
                            ->preload()
                            ->required()
                            // A MÁGICA: Seleciona automaticamente o primeiro profissional do estúdio!
                            ->default(fn() => Professional::where('studio_id', Filament::getTenant()->id)->first()?->id)
                            ->helperText('Selecione quem fará o atendimento.'),
                        DateTimePicker::make('starts_at')
                            ->required()
                            ->label('Início')
                            ->seconds(false)
                            ->displayFormat('d/m/Y H:i')
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

                        DateTimePicker::make('ends_at')
                            ->required()
                            ->label('Término (Com Buffer)')
                            ->seconds(false)
                            ->displayFormat('d/m/Y H:i')
                            ->helperText('O horário final já inclui o tempo de limpeza/buffer.')
                            ->rules([
                                function (Get $get) {
                                    return function (string $attribute, $value, $fail) use ($get) {
                                        $startsAt = $get('starts_at');
                                        $endsAt = $value;
                                        $appointmentId = $get('id');

                                        $exists = Appointment::where(function ($query) use ($startsAt, $endsAt) {
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
                                            $fail('Este horário já está ocupado.');
                                        }
                                    };
                                },
                            ]),

                        Select::make('location_id')
                            ->relationship('location', 'name')
                            ->label('Local do Atendimento')
                            ->required()
                            ->preload()
                            ->searchable(),

                        Select::make('status')
                            ->options([
                                'agendado' => 'Agendado',
                                'concluido' => 'Concluído',
                                'cancelado' => 'Cancelado',
                            ])
                            ->default('agendado')
                            ->required(),

                        Textarea::make('notes')
                            ->label('Observações')
                            ->columnSpanFull(),
                    ])
            ]);
    }
}
