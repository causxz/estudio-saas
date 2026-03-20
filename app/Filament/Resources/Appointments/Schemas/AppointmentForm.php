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

                        // --- CAMPO COMISSÃO E EQUIPE ---
                        Select::make('professional_id')
                            ->relationship(
                                name: 'professional',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn(Builder $query) => $query->where('studio_id', Filament::getTenant()->id)
                            )
                            ->label('Profissional Responsável (Opcional)')
                            ->searchable()
                            ->preload()             
                            ->default(fn() => Professional::where('studio_id', Filament::getTenant()->id)->first()?->id)
                            ->helperText('Deixe em branco se o estúdio não trabalhar com múltiplos profissionais.'),
                            
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
                                        $professionalId = $get('professional_id');
                                        $studioId = Filament::getTenant()->id;

                                        $exists = Appointment::where('studio_id', $studioId)
                                            ->where('status', '!=', 'cancelado')
                                            ->when($appointmentId, fn($q) => $q->where('id', '!=', $appointmentId)) // Ignora a si mesmo na edição
                                            ->when($professionalId, function ($query, $profId) {
                                                // Se tem profissional, olha só a agenda DELE
                                                return $query->where('professional_id', $profId);
                                            }, function ($query) {
                                                // Se NÃO tem profissional (estúdio de 1 pessoa), olha a agenda geral sem dono
                                                return $query->whereNull('professional_id');
                                            })
                                            ->where(function ($query) use ($startsAt, $endsAt) {
                                                // O novo começa ANTES do existente terminar E termina DEPOIS do existente começar
                                                $query->where('starts_at', '<', $endsAt)
                                                      ->where('ends_at', '>', $startsAt);
                                            })
                                            ->exists();

                                        if ($exists) {
                                            $fail('Este horário já está ocupado na agenda' . ($professionalId ? ' deste profissional.' : ' do estúdio.'));
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