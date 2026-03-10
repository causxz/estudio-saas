<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Appointment;
use App\Models\Client;
use Carbon\Carbon;

// 1. IMPORTANTE: Se o seu model de financeiro tiver outro nome (ex: Transaction, Receita, Pagamento), mude aqui!
use App\Models\Transaction as Financial; 

class DashboardStats extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $agendamentosHoje = Appointment::whereDate('starts_at', Carbon::today())->count();
        $clientesMes = Client::whereMonth('created_at', Carbon::now()->month)->count();
        $concluidos = Appointment::where('status', 'concluido')->count();

        // 2. CÁLCULO DO FATURAMENTO DO MÊS
        $faturamentoMes = 0;
        
        // Verifica se o Model existe para não quebrar a tela caso o nome esteja diferente
        if (class_exists(Financial::class)) {
            $faturamentoMes = Financial::whereMonth('created_at', Carbon::now()->month)
                // Se você tiver uma coluna indicando que é entrada/receita, descomente a linha abaixo e ajuste:
                // ->where('tipo', 'entrada') 
                ->sum('valor'); // IMPORTANTE: Mude 'valor' para o nome da sua coluna no banco de dados (ex: 'amount', 'price', etc)
        }

        return [
            Stat::make('Agendamentos para Hoje', $agendamentosHoje)
                ->description($agendamentosHoje > 0 ? 'Você tem clientes hoje!' : 'Nenhum agendamento ainda.')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color($agendamentosHoje > 0 ? 'success' : 'gray'),

            Stat::make('Novas Clientes (Mês)', $clientesMes)
                ->description('Cadastradas neste mês')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info')
                ->chart([7, 2, 10, 3, 15, 4, $clientesMes]), 

            Stat::make('Atendimentos Realizados', $concluidos)
                ->description('Total histórico concluído')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('primary'),

            // NOVO CARTÃO DE FATURAMENTO
            Stat::make('Faturamento (Mês)', 'R$ ' . number_format($faturamentoMes, 2, ',', '.'))
                ->description('Ganhos deste mês')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success')
                ->chart([150, 200, 100, 400, 300, 500, $faturamentoMes]), // Gráfico de linha verde subindo
        ];
    }
}