<?php

namespace App\Filament\Resources\PersonalTransactions;

use App\Filament\Resources\PersonalTransactions\Pages\ManagePersonalTransactions;
use App\Models\PersonalTransaction;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\RestoreAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ForceDeleteBulkAction;

// IMPORTAÇÕES CORRIGIDAS PARA O PADRÃO SCHEMA
use Filament\Schemas\Schema; 
use Filament\Schemas\Components\Section;

use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

// Componentes de Formulário (Inputs continuam no Forms)
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;

// Componentes de Tabela
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class PersonalTransactionResource extends Resource
{
    protected static ?string $model = PersonalTransaction::class;

    // Isola este recurso para não procurar pelo Studio (SaaS)
    protected static bool $isScopedToTenant = false;

    // Ícone elegante de carteira para o Financeiro Pessoal
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWallet;
    
    // Nomenclaturas personalizadas para o menu
    protected static ?string $navigationLabel = 'Financeiro Pessoal';
    protected static ?string $modelLabel = 'Movimentação Pessoal';
    protected static ?string $pluralModelLabel = 'Financeiro Pessoal';

    public static function getNavigationGroup(): ?string
    {
        return 'Meu Perfil';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Nova Movimentação Pessoal')
                    ->description('Organize seu dinheiro particular, totalmente separado do caixa do estúdio.')
                    ->schema([
                        // Salva silenciosamente o ID do usuário logado
                        Hidden::make('user_id')
                            ->default(fn () => auth()->id()),

                        TextInput::make('description')
                            ->label('Descrição')
                            ->placeholder('Ex: Supermercado, Aluguel, Saída Lazer...')
                            ->required()
                            ->columnSpanFull(),
                            
                        Select::make('type')
                            ->label('Tipo de Movimentação')
                            ->options([
                                'income' => 'Entrada (Pró-labore, Salário)',
                                'expense' => 'Saída (Gasto Pessoal)',
                            ])
                            ->required()
                            ->native(false),
                            
                        TextInput::make('amount')
                            ->label('Valor')
                            ->numeric()
                            ->prefix('R$')
                            ->required(),
                            
                        Select::make('category')
                            ->label('Categoria')
                            ->options([
                                'Moradia' => 'Moradia (Aluguel, Luz, Água)',
                                'Alimentação' => 'Alimentação',
                                'Transporte' => 'Transporte / Combustível',
                                'Lazer' => 'Lazer e Cuidados Pessoais',
                                'Saúde' => 'Saúde',
                                'Educação' => 'Educação',
                                'Outros' => 'Outros',
                            ])
                            ->searchable()
                            ->native(false),

                        DatePicker::make('date')
                            ->label('Data')
                            ->default(now())
                            ->displayFormat('d/m/Y')
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')
                    ->label('Data')
                    ->date('d/m/Y')
                    ->sortable(),
                    
                TextColumn::make('description')
                    ->label('Descrição')
                    ->searchable()
                    ->limit(30),
                    
                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'income' => 'success',
                        'expense' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'income' => 'Entrada',
                        'expense' => 'Saída',
                        default => $state,
                    }),

                TextColumn::make('category')
                    ->label('Categoria')
                    ->badge()
                    ->color('gray')
                    ->searchable(),
                    
                TextColumn::make('amount')
                    ->label('Valor')
                    ->money('BRL')
                    ->sortable()
                    ->weight('bold'),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Filtrar por Tipo')
                    ->options([
                        'income' => 'Apenas Entradas',
                        'expense' => 'Apenas Saídas',
                    ])->native(false),
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('date', 'desc'); // Mostra os mais recentes primeiro
    }
    
    // 🔒 SEGURANÇA MÁXIMA: Garante que uma Lash nunca veja os gastos da outra
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id())
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getPages(): array
    {
        return [
            // Como usamos --simple ao criar, tudo abre em modais limpos sem sair da tela
            'index' => ManagePersonalTransactions::route('/'),
        ];
    }
}