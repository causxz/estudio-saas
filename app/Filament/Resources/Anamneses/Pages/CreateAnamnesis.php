<?php

namespace App\Filament\Resources\Anamneses\Pages;

use App\Filament\Resources\Anamneses\AnamnesisResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAnamnesis extends CreateRecord
{
    protected static string $resource = AnamnesisResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
