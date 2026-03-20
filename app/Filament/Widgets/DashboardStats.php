<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Facades\Filament;

class DashboardStats extends BaseWidget
{
    protected static ?int $sort = 1; // Posição 1

    protected function getStats(): array
    {
        // 1. Pega o ID do estúdio logado atualmente (Blindagem Multi-Tenancy)
        $studioId = Filament::getTenant()->id;

        // 2. Filtra todas as métricas anexando o where('studio_id', $studioId)
        $agendamentosHoje = Appointment::where('studio_id', $studioId)
            ->whereDate('starts_at', Carbon::today())
            ->count();
            
        $clientesMes = Client::where('studio_id', $studioId)
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();
            
        $concluidos = Appointment::where('studio_id', $studioId)
            ->where('status', 'concluido')
            ->count();

        // CÁLCULO DO FATURAMENTO DO MÊS
        $faturamentoMes = 0;
        
        if (class_exists(Transaction::class)) {
            $faturamentoMes = Transaction::where('studio_id', $studioId)
                ->whereMonth('transaction_date', Carbon::now()->month)
                ->where('type', 'entrada') 
                ->sum('amount');
        }

        return [
            Stat::make('Agendamentos para Hoje', $agendamentosHoje)
                ->description($agendamentosHoje > 0 ? 'Tem clientes hoje!' : 'Nenhum agendamento.')
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

            Stat::make('Faturamento (Mês)', 'R$ ' . number_format((float)$faturamentoMes, 2, ',', '.'))
                ->description('Ganhos deste mês')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success')
                ->chart([150, 200, 100, 400, 300, 500, (float) $faturamentoMes]),
        ];
    }
}