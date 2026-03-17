<?php

namespace App\Filament\Resources\Clients;

use Filament\Support\Icons\Heroicon;
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

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\HtmlString;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;
    protected static ?string $modelLabel = 'Cliente';
    protected static ?string $pluralModelLabel = 'Clientes';
    protected static ?string $navigationLabel = 'Clientes';
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

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
                            ->label('WhatsApp')
                            ->tel() 
                            ->maxLength(32) 
                            ->extraInputAttributes(['oninput' => "this.value = this.value.replace(/[^0-9+]/g, '')"])
                            ->helperText('Apenas números. Você pode usar o sinal de + para números internacionais.'),
                                                    
                        Textarea::make('preferences_summary')
                            ->label('Notas Fixas de Estilo (Preferências)')
                            ->helperText('Ex: Gosta de curvatura D, fios 12mm. Olho sensível.')
                            ->columnSpanFull(),

                        Placeholder::make('return_alert')
                            ->label('Status de Retenção')
                            ->content(function ($record) {
                                if (! $record) return '-';

                                $alert = $record->return_alert;

                                if (str_contains($alert, 'Agendado')) {
                                    return new HtmlString("<span style='color: #10b981; font-weight: bold;'>{$alert}</span>");
                                } elseif (str_contains($alert, 'há')) {
                                    preg_match('/\d+/', $alert, $matches);
                                    $days = $matches[0] ?? 0;

                                    if ($days > 20) {
                                        return new HtmlString("<span style='color: #ef4444; font-weight: bold;'>⚠️ {$alert} (Atenção: Passou do prazo de manutenção)</span>");
                                    }
                                    return new HtmlString("<span style='color: #f59e0b; font-weight: bold;'>{$alert}</span>");
                                }

                                return $alert;
                            })
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
                TextColumn::make('whatsapp')
                    ->label('WhatsApp'),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\AnamnesesRelationManager::class,
            RelationManagers\AppointmentsRelationManager::class,
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
