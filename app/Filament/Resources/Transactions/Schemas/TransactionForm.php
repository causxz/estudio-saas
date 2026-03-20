<?php

namespace App\Filament\Resources\Transactions\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Utilities\Get;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detalhes da Movimentação')
                    ->columns(2)
                    ->schema([
                        Select::make('appointment_id')
                            ->label('Agendamento Vinculado (Opcional)')
                            ->relationship('appointment', 'id')
                            ->getOptionLabelFromRecordUsing(fn($record) => "Agendamento #{$record->id} - " . ($record->client?->name ?? 'Sem cliente') . " (" . \Carbon\Carbon::parse($record->starts_at)->format('d/m/Y') . ")")->searchable()
                            ->preload()
                            ->helperText('Vincule a um agendamento para o sistema calcular a comissão automaticamente.')
                            ->live(),

                        Select::make('type')
                            ->label('Tipo de Movimentação')
                            ->options([
                                'entrada' => 'Entrada (Receita)',
                                'saida' => 'Saída (Despesa / Comissão)',
                            ])
                            ->required()
                            ->live()
                            ->native(false),

                        TextInput::make('description')
                            ->label('Descrição')
                            ->placeholder('Ex: Pagamento de Comissão, Compra de Material...')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('amount')
                            ->label('Valor')
                            ->numeric()
                            ->prefix('R$')
                            ->required(),

                        DatePicker::make('transaction_date')
                            ->label('Data da Movimentação')
                            ->default(date('Y-m-d'))
                            ->required(),

                        Select::make('payment_method')
                            ->label('Método de Pagamento')
                            ->options([
                                'pix' => 'PIX',
                                'cartao_credito' => 'Cartão de Crédito',
                                'cartao_debito' => 'Cartão de Débito',
                                'dinheiro' => 'Dinheiro',
                            ])
                            ->native(false),

                        Select::make('professional_id')
                            ->label('Profissional (Destinatário)')
                            ->relationship('professional', 'name')
                            ->searchable()
                            ->preload()
                            ->helperText('Use este campo apenas para Vales, Bônus ou pagamentos manuais. As comissões de serviços são geradas automaticamente.')->visible(fn(Get $get): bool => $get('type') === 'saida'),

                        Textarea::make('notes')
                            ->label('Observações Adicionais')
                            ->columnSpanFull(),
                    ])
            ]);
    }
}
