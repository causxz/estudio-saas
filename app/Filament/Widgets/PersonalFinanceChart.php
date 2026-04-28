<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\PersonalTransaction;

class PersonalFinanceChart extends ChartWidget
{
    protected ?string $heading = 'Personal Finance Chart';

    protected function getData(): array
    {
        $userId = auth()->id();

        // Lógica simples para pegar gastos vs entradas dos últimos 6 meses
        $data = PersonalTransaction::where('user_id', $userId)
            ->selectRaw("type, SUM(amount) as total, MONTH(date) as month")
            ->groupBy('type', 'month')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Entradas',
                    'data' => $data->where('type', 'income')->pluck('total')->toArray(),
                    'borderColor' => '#844D36', // Seu tom Terra
                ],
                [
                    'label' => 'Gastos',
                    'data' => $data->where('type', 'expense')->pluck('total')->toArray(),
                    'borderColor' => '#C28E64', // Seu tom Bronze
                ],
            ],
            'labels' => ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
