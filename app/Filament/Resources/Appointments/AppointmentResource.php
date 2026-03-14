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

use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

use Filament\Actions\Action;
use Filament\Actions\EditAction;

use Illuminate\Support\Facades\Http;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;
    protected static ?string $modelLabel = 'Agendamento';
    protected static ?string $pluralModelLabel = 'Agendamentos';
    protected static ?string $navigationLabel = 'Agendamentos';

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
                                        $totalMinutes = $service->duration_minutes + ($service->buffer_after ?? 0);
                                        $set('ends_at', Carbon::parse($startsAt)->addMinutes($totalMinutes)->toDateTimeString());
                                    }
                                }
                            }),

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
                                            $fail('Este horário (incluindo a margem de limpeza) já está ocupado.');
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
                                'confirmado' => 'Confirmado',
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('client.name')->label('Cliente')->searchable(),
                TextColumn::make('service.name')->label('Serviço'),
                TextColumn::make('starts_at')->label('Início')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('status')->badge()->color(fn($state) => match ($state) {
                    'agendado' => 'warning',
                    'confirmado' => 'success',
                    'concluido' => 'info',
                    'cancelado' => 'danger',
                    default => 'gray'
                }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Filtrar por Status')
                    ->options([
                        'agendado' => '📅 Agendado',
                        'confirmado' => '✅ Confirmado',
                        'concluido' => '✨ Concluído',
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

                Action::make('confirmar_whatsapp')
                    ->label('WhatsApp')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->color('success')
                    ->form([
                        Select::make('local_atendimento')
                            ->label('Onde será o atendimento?')
                            ->options([
                                // AQUI FICA A PERSONALIZAÇÃO PARA A LOUYSE LASH DESIGN:
                                'Espaço Matriz' => 'Espaço Louyse Lash - Matriz (Insira a Rua aqui)',
                                'Espaço Filial' => 'Espaço Louyse Lash - Filial (Insira a Rua aqui)',
                                'Atendimento a Domicílio' => 'Atendimento a Domicílio (Iremos até você!)',
                            ])
                            ->required(),

                        Textarea::make('observacao')
                            ->label('Observação extra (Opcional)')
                            ->placeholder('Ex: Venha com os cílios limpos e sem rímel...')
                    ])
                    ->action(function ($record, array $data) {

                        $dataAgendamento = Carbon::parse($record->starts_at)->format('d/m/Y');
                        $horaAgendamento = Carbon::parse($record->starts_at)->format('H:i');

                        $nomeCliente = explode(' ', trim($record->client->name))[0];

                        $local = $data['local_atendimento'];
                        $obs = $data['observacao'] ? "\n📌 *Aviso:* {$data['observacao']}" : '';

                        $mensagem = "Olá, *{$nomeCliente}*! ✨\n\nPassando para confirmar o seu agendamento de *{$record->service->name}* conosco na Louyse Lash Design.\n\n📅 *Data:* {$dataAgendamento}\n⏰ *Horário:* {$horaAgendamento}\n📍 *Local:* {$local}{$obs}\n\nPor favor, confirme respondendo a esta mensagem com *SIM* ou *NÃO*.\n\nAté logo! 💕";

                        $telefoneRaw = $record->client->whatsapp ?? '';
                        $numeroLimpo = preg_replace('/[^0-9]/', '', $telefoneRaw);

                        if (empty($numeroLimpo) || strlen($numeroLimpo) < 10) {
                            Notification::make()->title('Erro')->body('Cliente sem número de WhatsApp válido.')->danger()->send();
                            return;
                        }

                        if (strlen($numeroLimpo) <= 11) {
                            $numeroLimpo = '55' . $numeroLimpo;
                        }

                        $response = Http::withHeaders([
                            'apikey' => 'ChaveSecretaEstudio123',
                            'Content-Type' => 'application/json'
                        ])->post("http://localhost:8080/message/sendText/estudio", [
                            'number' => $numeroLimpo,
                            'text' => $mensagem,
                            'textMessage' => [
                                'text' => $mensagem
                            ],
                            'options' => [
                                'delay' => 1500,
                                'presence' => 'composing'
                            ]
                        ]);

                        if ($response->successful()) {
                            $record->update(['status' => 'confirmado']);

                            Notification::make()
                                ->title('Enviado!')
                                ->body('Mensagem de confirmação enviada com sucesso!')
                                ->success()
                                ->send();
                        } else {
                            $erro = $response->json('response.message') ?? 'Erro desconhecido';
                            if (is_array($erro)) $erro = json_encode($erro);

                            Notification::make()
                                ->title('Falha ao enviar')
                                ->body("Verifique a Evolution API. Erro: {$erro}")
                                ->danger()
                                ->persistent()
                                ->send();
                        }
                    })
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
