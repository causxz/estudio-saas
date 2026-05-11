<?php

namespace App\Filament\Resources\PersonalTransactions\Pages;

use App\Filament\Resources\PersonalTransactions\PersonalTransactionResource;
use App\Filament\Widgets\PersonalFinanceChart;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePersonalTransactions extends ManageRecords
{
    protected static string $resource = PersonalTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Nova Movimentação'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PersonalFinanceChart::class,
        ];
    }
}
