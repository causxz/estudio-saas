<?php

namespace App\Filament\Resources\Transactions;

use Filament\Support\Icons\Heroicon;
use App\Filament\Resources\Transactions\Pages;
use App\Models\Transaction;
use App\Models\Appointment;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Filament\Tables\Filters\Filter;
use Filament\Facades\Filament;

use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\Summarizers\Sum; 

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;
    
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCurrencyDollar;
    
    protected static ?string $modelLabel = 'Transação';
    protected static ?string $pluralModelLabel = 'Fluxo de Caixa';
    protected static ?string $navigationLabel = 'Financeiro';
    protected static ?int $navigationSort = 4;

    public static function canViewAny(): bool
    {
        $tenant = Filament::getTenant();
        $user = auth()->user();

        return $tenant && $tenant->users()->where('user_id', $user->id)->exists(); 
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detalhes da Movimentação')
                    ->columns(2)
                    ->schema([
                        Select::make('type')
                            ->label('Tipo de Movimentação')
                            ->options([
                                'entrada' => '🟢 Entrada (Receita)',
                                'saida' => '🔴 Saída (Despesa / Comissão)',
                            ])
                            ->required()
                            ->default('entrada')
                            ->live(),
                        
                        Select::make('appointment_id')
                            ->label('Vincular a um Agendamento (Opcional)')
                            ->relationship(
                                name: 'appointment', 
                                titleAttribute: 'id',
                                modifyQueryUsing: function (Builder $query, ?Transaction $record) {
                                    $query->where('studio_id', Filament::getTenant()->id);
                                    $query->whereDoesntHave('transaction');
                                    
                                    if ($record && $record->appointment_id) {
                                        $query->orWhere('id', $record->appointment_id);
                                    }
                                    
                                    return $query;
                                }
                            )
                            ->getOptionLabelFromRecordUsing(fn (Appointment $record) => ($record->client?->name ?? 'Sem cliente') . ' - ' . \Carbon\Carbon::parse($record->starts_at)->format('d/m/Y H:i'))
                            ->searchable()
                            ->preload()
                            ->columnSpanFull()
                            ->live() 
                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                if ($state) {
                                    $appointment = Appointment::with('service')->find($state);
                                    
                                    if ($appointment && $appointment->service) {
                                        $set('type', 'entrada');
                                        $set('description', 'Pagamento: ' . $appointment->service->name);
                                        $set('amount', $appointment->service->price);
                                        $set('transaction_date', \Carbon\Carbon::parse($appointment->starts_at)->format('Y-m-d'));
                                    }
                                }
                            }),
                            
                        TextInput::make('description')
                            ->label('Descrição')
                            ->required()
                            ->placeholder('Ex: Pagamento de Comissão, Compra de Fios, Aluguel...')
                            ->maxLength(255)
                            ->columnSpanFull(),

                        TextInput::make('amount')
                            ->label('Valor (R$)')
                            ->required()
                            ->numeric()
                            ->prefix('R$')
                            ->maxValue(99999999.99),

                        Select::make('payment_method')
                            ->label('Forma de Pagamento')
                            ->options([
                                'pix' => 'Pix',
                                'cartao_credito' => 'Cartão de Crédito',
                                'cartao_debito' => 'Cartão de Débito',
                                'dinheiro' => 'Dinheiro (Espécie)',
                            ])
                            ->required()
                            ->live(),
                
                        Select::make('professional_id')
                            ->label('Profissional (Destinatário)')
                            ->relationship(
                                name: 'professional',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn(Builder $query) => $query->where('studio_id', Filament::getTenant()->id)
                            )
                            ->searchable()
                            ->preload()
                            ->helperText('Preencha para registrar o pagamento de comissões ou vales.')
                            ->visible(fn(Get $get): bool => $get('type') === 'saida'),

                        TextInput::make('amount_received')
                            ->label('Valor Recebido (R$)')
                            ->numeric()
                            ->prefix('R$')
                            ->live(onBlur: true)
                            ->dehydrated(false)
                            ->hidden(fn (Get $get) => $get('payment_method') !== 'dinheiro')
                            ->helperText('Digite o valor da nota que a cliente entregou.')
                            ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                $valorServico = (float) $get('amount');
                                $valorRecebido = (float) $state;
                                
                                if ($valorRecebido > $valorServico) {
                                    $set('change_amount', number_format($valorRecebido - $valorServico, 2, '.', ''));
                                } else {
                                    $set('change_amount', null);
                                }
                            }),

                        TextInput::make('change_amount')
                            ->label('Troco a Devolver')
                            ->prefix('R$')
                            ->readOnly()
                            ->dehydrated(false)
                            ->hidden(fn (Get $get) => $get('payment_method') !== 'dinheiro'),

                        DatePicker::make('transaction_date')
                            ->label('Data da Transação')
                            ->required()
                            ->default(now())
                            ->displayFormat('d/m/Y'),

                        Textarea::make('notes')
                            ->label('Observações Adicionais')
                            ->columnSpanFull(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('transaction_date')
                    ->label('Data')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'entrada' => 'success',
                        'saida' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                TextColumn::make('professional.name')
                    ->label('Profissional')
                    ->searchable()
                    ->placeholder('-'),

                TextColumn::make('description')
                    ->label('Descrição')
                    ->searchable(),

                TextColumn::make('amount')
                    ->label('Valor')
                    ->money('BRL')
                    ->sortable()
                    ->summarize(Sum::make()->money('BRL')->label('Total')),

                TextColumn::make('payment_method')
                    ->label('Pagamento')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'pix' => 'Pix',
                        'cartao_credito' => 'Cartão de Crédito',
                        'cartao_debito' => 'Cartão de Débito',
                        'dinheiro' => 'Dinheiro',
                        default => '-',
                    }),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Filtrar por Tipo')
                    ->options([
                        'entrada' => 'Apenas Entradas',
                        'saida' => 'Apenas Saídas',
                    ]),
                
                SelectFilter::make('professional_id')
                    ->label('Filtrar por Profissional')
                    ->relationship(
                        name: 'professional', 
                        titleAttribute: 'name', 
                        modifyQueryUsing: fn (Builder $query) => $query->where('studio_id', Filament::getTenant()->id)
                    )
                    ->searchable()
                    ->preload(),

                Filter::make('periodo')
                    ->form([
                        Select::make('period')
                            ->label('Período de Tempo')
                            ->options([
                                'hoje' => 'Hoje',
                                'esta_semana' => 'Esta Semana',
                                'este_mes' => 'Este Mês',
                                'mes_passado' => 'Mês Passado',
                                'este_semestre' => 'Últimos 6 Meses',
                                'este_ano' => 'Este Ano',
                            ])
                            ->default('este_mes'), 
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['period'])) {
                            return $query;
                        }

                        return match ($data['period']) {
                            'hoje' => $query->whereDate('transaction_date', Carbon::today()),
                            'esta_semana' => $query->whereBetween('transaction_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]),
                            'este_mes' => $query->whereBetween('transaction_date', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]),
                            'mes_passado' => $query->whereBetween('transaction_date', [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()]),
                            'este_semestre' => $query->whereBetween('transaction_date', [Carbon::now()->subMonths(6)->startOfDay(), Carbon::now()->endOfDay()]),
                            'este_ano' => $query->whereBetween('transaction_date', [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()]),
                            default => $query,
                        };
                    }),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('transaction_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}