<?php

namespace App\Filament\Resources\Clients;

use App\Filament\Resources\Clients\Pages;
use App\Filament\Resources\Clients\RelationManagers;
use App\Models\Client;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;
    
    protected static ?string $modelLabel = 'Cliente';

    // A TIPAGEM EXATA QUE O PHP EXIGE:
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Dados da Cliente')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->label('Nome Completo'),
                        TextInput::make('whatsapp')
                            ->label('WhatsApp'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->label('Nome'),
                TextColumn::make('whatsapp')
                    ->label('WhatsApp'),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        // O Intelephense vai sublinhar isso aqui até rodarmos o Passo 2. Pode ignorar por enquanto!
        return [
            RelationManagers\AnamnesesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}