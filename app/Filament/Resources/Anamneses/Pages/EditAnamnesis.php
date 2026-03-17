<?php

namespace App\Filament\Resources\Anamneses\Pages;

use App\Filament\Resources\Anamneses\AnamnesisResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAnamnesis extends EditRecord
{
    protected static string $resource = AnamnesisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
