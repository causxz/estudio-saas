<?php

namespace App\Filament\Resources\Locations;

use Filament\Support\Icons\Heroicon;
use App\Filament\Resources\Locations\Pages;
use App\Models\Location;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;

class LocationResource extends Resource
{
    protected static ?string $model = Location::class;
    protected static ?string $modelLabel = 'Local de Atendimento';
    protected static ?string $pluralModelLabel = 'Locais de Atendimento';
    protected static ?string $navigationLabel = 'Locais';
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detalhes do Local')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->label('Nome do Local')
                            ->placeholder('Ex: Matriz, Filial Shopping, Domicílio'),

                        TextInput::make('address')
                            ->required()
                            ->label('Endereço Completo')
                            ->placeholder('Ex: Rua das Flores, 123 - Centro'),

                        TextInput::make('maps_link')
                            ->label('Link do Google Maps')
                            ->url()
                            ->placeholder('Ex: https://maps.google.com/...')
                            ->columnSpanFull(),
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
                TextColumn::make('address')
                    ->label('Endereço'),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLocations::route('/'),
            'create' => Pages\CreateLocation::route('/create'),
            'edit' => Pages\EditLocation::route('/{record}/edit'),
        ];
    }
}