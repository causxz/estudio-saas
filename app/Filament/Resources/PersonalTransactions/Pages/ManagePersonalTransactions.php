<?php

namespace App\Filament\Resources\PersonalTransactions\Pages;

use App\Filament\Resources\PersonalTransactions\PersonalTransactionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManagePersonalTransactions extends ManageRecords
{
    protected static string $resource = PersonalTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
