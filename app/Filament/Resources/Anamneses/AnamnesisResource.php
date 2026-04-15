<?php

namespace App\Filament\Resources\Anamneses;

use Filament\Support\Icons\Heroicon;
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
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

// Tabela e Ações
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
// use Barryvdh\DomPDF\Facade\Pdf;
// use Illuminate\Support\Facades\Storage;
use Filament\Actions\Action;

use BackedEnum;

class AnamnesisResource extends Resource
{
    protected static ?string $model = Anamnesis::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;
    protected static ?string $navigationLabel = 'Anamneses';
    protected static ?string $modelLabel = 'Anamnese';
    protected static ?string $pluralModelLabel = 'Anamneses';
    protected static ?string $tenantRelationshipName = 'anamneses';


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

                Section::make('Procedimento Realizado')
                    ->columns(2)
                    ->schema([
                        TextInput::make('preferred_style')
                            ->label('Estilo (Ex: Fio a fio, Volume Russo, etc.)'),
                        TextInput::make('mapping_details')
                            ->label('Mapping (Ex: 8, 9, 10, 11)'),
                        Textarea::make('observations')
                            ->label('Observações Adicionais')
                            ->columnSpanFull(),
                    ]),

                // Section::make('Ficha Física / Digitalização')
                //     ->schema([
                //         FileUpload::make('physical_file')
                //             ->label('Upload da Ficha (PDF/Foto)')
                //             ->disk('public')
                //             ->directory('anamneses')
                //             ->acceptedFileTypes(['application/pdf', 'image/*'])
                //             ->downloadable()
                //             ->openable()
                //             ->getUploadedFileNameForStorageUsing(function (\Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file, \Filament\Schemas\Components\Utilities\Get $get): string {

                //                 $clientId = $get('client_id');
                //                 $nomeCliente = 'cliente_sem_nome';

                //                 if ($clientId) {
                //                     $cliente = \App\Models\Client::find($clientId);
                //                     if ($cliente) {
                //                         $nomeCliente = \Illuminate\Support\Str::slug($cliente->name);
                //                     }
                //                 }

                //                 $extensao = $file->getClientOriginalExtension();
                //                 $dataUpload = now()->format('d-m-Y_H-i');

                //                 return "{$nomeCliente}_{$dataUpload}.{$extensao}";
                //             }),
                //     ]),

                Section::make('Termo de Responsabilidade')
                    ->schema([
                        \Saade\FilamentAutograph\Forms\Components\SignaturePad::make('signature')
                            ->label('Assinatura da Cliente')
                            ->penColor('#ffffff')
                            ->penColorOnDark('#000000')
                            ->backgroundColor('#313131')
                            ->backgroundColorOnDark('#ffffff')
                            ->clearable()
                            ->required()
                            ->columnSpanFull(),
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

                // NOVO BOTÃO: Abrir Ficha Preenchida na Tela
                Action::make('imprimir')
                    ->label('Ficha')
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->url(fn(Anamnesis $record) => route('anamnese.imprimir', $record->id))
                    ->openUrlInNewTab(), // Abre numa aba nova para não fechar o sistema
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
