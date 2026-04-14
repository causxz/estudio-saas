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
                        
                        // 1. CLIENTE (Blindado para o Estúdio Atual)
                        Select::make('client_id')
                            ->relationship(
                                name: 'client', 
                                titleAttribute: 'name',
                                modifyQueryUsing: fn(Builder $query) => $query->where('studio_id', Filament::getTenant()->id)
                                ->withoutTrashed()
                            )
                            ->searchable()
                            ->preload()
                            
                            ->required()
                            ->label('Cliente'),

                        // 2. SERVIÇO (Blindado para o Estúdio Atual)
                        Select::make('service_id')
                            ->relationship(
                                name: 'service', 
                                titleAttribute: 'name',
                                modifyQueryUsing: fn(Builder $query) => $query->where('studio_id', Filament::getTenant()->id)
                                ->withoutTrashed()
                            )
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

                        // 3. INÍCIO (Travado contra o passado)
                        DateTimePicker::make('starts_at')
                            ->label('Início do Agendamento')
                            ->required()
                            ->seconds(false)
                            ->displayFormat('d/m/Y H:i')
                            ->minDate(now()) // Bloqueia datas retroativas
                            ->native(false)
                            ->live()
                            -> closeOnDateSelection()
                            ->minutesStep(10)
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

                        // 4. FIM (Travado para ser DEPOIS do início e com verificação de conflito)
                        DateTimePicker::make('ends_at')
                            ->label('Término (Com Buffer)')
                            ->required()
                            ->seconds(false)
                            ->displayFormat('d/m/Y H:i')
                            ->after('starts_at') // Garante cronologia correta
                            ->native(false)
                            ->helperText('O horário final já inclui o tempo de limpeza/buffer.')
                            ->rules([
                                fn(Get $get, ?Appointment $record) => function (string $attribute, $value, $fail) use ($get, $record) {
                                    $startsAtRaw = $get('starts_at');
                                    $endsAtRaw = $value;

                                    if (!$startsAtRaw || !$endsAtRaw) return;

                                    $startsAt = Carbon::parse($startsAtRaw)->format('Y-m-d H:i:s');
                                    $endsAt = Carbon::parse($endsAtRaw)->format('Y-m-d H:i:s');

                                    $appointmentId = $record?->id;
                                    $professionalId = $get('professional_id');
                                    $studioId = Filament::getTenant()->id;

                                    $exists = Appointment::where('studio_id', $studioId)
                                        ->where('status', '!=', 'cancelado')
                                        ->when($appointmentId, fn($q) => $q->where('id', '!=', $appointmentId))
                                        ->when($professionalId, function ($query) use ($professionalId) {
                                            return $query->where('professional_id', $professionalId);
                                        }, function ($query) {
                                            return $query->whereNull('professional_id');
                                        })
                                        ->where(function ($query) use ($startsAt, $endsAt) {
                                            $query->where('starts_at', '<', $endsAt)
                                                ->where('ends_at', '>', $startsAt);
                                        })
                                        ->exists();

                                    if ($exists) {
                                        $fail('Este horário já está ocupado na agenda' . ($professionalId ? ' desta profissional.' : ' do estúdio.'));
                                    }
                                }
                            ]),

                        // 5. PROFISSIONAL 
                        Select::make('professional_id')
                            ->relationship(
                                name: 'professional',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn(Builder $query) => $query->where('studio_id', Filament::getTenant()->id)
                                ->withoutTrashed()
                            )
                            ->label('Profissional Responsável (Opcional)')
                            ->searchable()
                            ->preload()                    
                            ->live()
                            ->default(fn() => Professional::where('studio_id', Filament::getTenant()->id)->first()?->id)
                            ->helperText('Deixe em branco se o estúdio não trabalhar com múltiplos profissionais.'),

                        // 6. LOCAL (Blindado para o Estúdio Atual)
                        Select::make('location_id')
                            ->relationship(
                                name: 'location', 
                                titleAttribute: 'name',
                                modifyQueryUsing: fn(Builder $query) => $query->where('studio_id', Filament::getTenant()->id)
                                ->withoutTrashed()
                            )
                            ->label('Local do Atendimento')
                            ->required()
                            ->preload()
                            ->searchable(),

                        // 7. STATUS e OBSERVAÇÕES
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