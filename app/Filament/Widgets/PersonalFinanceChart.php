<?php

namespace App\Filament\Widgets;

use App\Models\PersonalTransaction;
use Filament\Widgets\ChartWidget;

class PersonalFinanceChart extends ChartWidget
{
    // Título do widget
    protected ?string $heading = 'Gastos por Categoria (Mês Atual)';
    
    //  Segurança e Tenant
    protected static bool $isScopedToTenant = false;

    // IMPEDE QUE O GRÁFICO APAREÇA NA DASHBOARD PRINCIPAL 
    protected static bool $isDiscovered = false;

    // Fixa a altura para não distorcer o visual
    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $userId = auth()->id();

        // Puxa gastos agrupados por categoria
        $data = PersonalTransaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereMonth('date', now()->month)
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->pluck('total', 'category')
            ->toArray();

        return [
            'datasets' => [
                [
                    'data' => array_values($data),
                    'backgroundColor' => ['#844D36', '#C28E64', '#6B3728', '#452B1F', '#E8C9A8', '#F7EDE0', '#9CA3AF'],
                    'borderWidth' => 0,
                ],
            ],
            'labels' => array_keys($data),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut'; // Estilo pizza/rosca
    }

    protected function getOptions(): array
    {
        return [
            // Mantém a proporção e impede que o gráfico tente usar 100% da tela
            'maintainAspectRatio' => false, 
            'plugins' => [
                'legend' => [
                    'position' => 'bottom', // Legendas embaixo para ganhar espaço
                ],
            ],
            'cutout' => '65%', // Afina a rosca para ficar mais elegante
        ];
    }
}