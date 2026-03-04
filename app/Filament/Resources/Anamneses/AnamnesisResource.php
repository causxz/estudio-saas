<?php

namespace App\Filament\Resources\Anamneses;

use App\Filament\Resources\Anamneses\Pages\CreateAnamnesis;
use App\Filament\Resources\Anamneses\Pages\EditAnamnesis;
use App\Filament\Resources\Anamneses\Pages\ListAnamneses;
use App\Models\Anamnesis;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

// Layouts e Campos
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea; // <-- Adicionado para as observações
use Filament\Forms\Components\TextInput; // <-- Adicionado para estilo e mapping

// Tabela e Ações
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

use BackedEnum;

class AnamnesisResource extends Resource
{
    protected static ?string $model = Anamnesis::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identificação')
                    ->schema([
                        Select::make('client_id')
                            ->relationship('client', 'name')
                            ->required()
                            ->label('Selecione a Cliente'),
                    ]),

                Section::make('Saúde e Alergias')
                    ->columns(2)
                    ->schema([
                        Toggle::make('has_allergy')->label('Possui Alergias?'),
                        Toggle::make('eye_disease')->label('Problemas Oculares?'),
                        Toggle::make('pregnant_or_lactating')->label('Grávida ou Lactante?'),
                        Toggle::make('uses_contact_lenses')->label('Usa Lentes?'),
                        Toggle::make('thyroid_problem')->label('Problema de Tireoide?'),
                        Toggle::make('sleeps_on_stomach')->label('Dorme de Bruços?'),
                    ]),

                // NOVA SESSÃO ADICIONADA AQUI:
                Section::make('Procedimento Realizado')
                    ->columns(2)
                    ->schema([
                        TextInput::make('preferred_style')
                            ->label('Estilo (Ex: Fio a fio, Volume Russo, etc.)'),
                        TextInput::make('mapping_details')
                            ->label('Mapping (Ex: 8, 9, 10, 11)'),
                        Textarea::make('observations')
                            ->label('Observações Adicionais')
                            ->columnSpanFull(), // Faz o campo ocupar a linha inteira
                    ]),

                Section::make('Ficha Física / Digitalização')
                    ->schema([
                        FileUpload::make('physical_file')
                            ->label('Upload da Ficha (PDF/Foto)')
                            ->disk('public')
                            ->directory('anamneses')
                            ->acceptedFileTypes(['application/pdf', 'image/*'])
                            ->downloadable()
                            ->openable()
                            // A MÁGICA DO NOME DO ARQUIVO COMEÇA AQUI:
                            ->getUploadedFileNameForStorageUsing(function (\Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file, \Filament\Schemas\Components\Utilities\Get $get): string {
                                
                                $clientId = $get('client_id');
                                $nomeCliente = 'cliente_sem_nome';

                                if ($clientId) {
                                    $cliente = \App\Models\Client::find($clientId);
                                    if ($cliente) {
                                        $nomeCliente = \Illuminate\Support\Str::slug($cliente->name);
                                    }
                                }

                                $extensao = $file->getClientOriginalExtension();
                                $dataUpload = now()->format('d-m-Y_H-i');

                                return "{$nomeCliente}_{$dataUpload}.{$extensao}";
                            }),
                    ]),
                    
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('client.name')
                    ->label('Cliente')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Data da Ficha')
                    ->date('d/m/Y'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAnamneses::route('/'),
            'create' => CreateAnamnesis::route('/create'),
            'edit' => EditAnamnesis::route('/{record}/edit'),
        ];
    }
}
