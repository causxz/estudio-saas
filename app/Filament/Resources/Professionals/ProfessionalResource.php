<?php

namespace App\Filament\Resources\Professionals;

use App\Models\Professional;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use App\Filament\Resources\Professionals\Schemas\ProfessionalForm;
use App\Filament\Resources\Professionals\Pages;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Tables\Filters\TrashedFilter;

class ProfessionalResource extends Resource
{
    protected static ?string $model = Professional::class;

    protected static ?string $modelLabel = 'Profissional';
    protected static ?string $pluralModelLabel = 'Equipe / Profissionais';
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedUser;

    // Relacionamento com o Estúdio
    protected static ?string $tenantOwnershipRelationshipName = 'studio';

    public static function getNavigationGroup(): ?string
    {
        return 'Gestão do Estúdio';
    }

    public static function form(Schema $schema): Schema
    {
        return ProfessionalForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nome')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('Telefone')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Cadastrada em')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make()->modalWidth('md'),
                DeleteAction::make(),
                RestoreAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProfessionals::route('/'),
        ];
    }
}
