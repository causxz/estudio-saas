<?php

namespace App\Filament\Resources\Transactions\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon; 

class TransactionStats extends StatsOverviewWidget
{
    // 1. CRIA O MENU DE FILTROS (DROPDOWN)
    protected function getFilters(): ?array
    {
        return [
            'hoje' => 'Hoje',
            'esta_semana' => 'Esta Semana',
            'este_mes' => 'Este Mês',
            'mes_passado' => 'Mês Passado',
            'este_semestre' => 'Últimos 6 Meses',
            'este_ano' => 'Este Ano',
            'tudo' => 'Todo o Período',
        ];
    }

    protected function getStats(): array
    {
        // 2. DESCOBRE QUAL FILTRO ESTÁ SELECIONADO (O padrão ao abrir a tela é 'este_mes')
        $activeFilter = $this->filter ?? 'este_mes';

        // 3. PREPARA A BUSCA NO BANCO DE DADOS
        $query = \App\Models\Transaction::query();

        // 4. APLICA A REGRA DE DATA BASEADA NA ESCOLHA DELA
        match ($activeFilter) {
            'hoje' => $query->whereDate('transaction_date', Carbon::today()),
            
            'esta_semana' => $query->whereBetween('transaction_date', [
                Carbon::now()->startOfWeek(), 
                Carbon::now()->endOfWeek()
            ]),
            
            'este_mes' => $query->whereBetween('transaction_date', [
                Carbon::now()->startOfMonth(), 
                Carbon::now()->endOfMonth()
            ]),
            
            'mes_passado' => $query->whereBetween('transaction_date', [
                Carbon::now()->subMonth()->startOfMonth(), 
                Carbon::now()->subMonth()->endOfMonth()
            ]),
            
            'este_semestre' => $query->whereBetween('transaction_date', [
                Carbon::now()->subMonths(6)->startOfDay(), 
                Carbon::now()->endOfDay()
            ]),
            
            'este_ano' => $query->whereBetween('transaction_date', [
                Carbon::now()->startOfYear(), 
                Carbon::now()->endOfYear()
            ]),
            
            'tudo' => $query, // Pega o histórico inteiro
        };

        // 5. CLONA A BUSCA PARA NÃO MISTURAR ENTRADAS E SAÍDAS
        $entradas = (clone $query)->where('type', 'entrada')->sum('amount');
        $saidas = (clone $query)->where('type', 'saida')->sum('amount');
        $saldo = $entradas - $saidas;

        // 6. DEVOLVE OS CARDS PRONTOS PARA A TELA
        return [
            Stat::make('Entradas', 'R$ ' . number_format($entradas, 2, ',', '.'))
                ->description('Receitas no período')
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->color('success'),

            Stat::make('Saídas', 'R$ ' . number_format($saidas, 2, ',', '.'))
                ->description('Despesas no período')
                ->descriptionIcon('heroicon-o-arrow-trending-down')
                ->color('danger'),

            Stat::make('Saldo Líquido', 'R$ ' . number_format($saldo, 2, ',', '.'))
                ->description('Lucro no período')
                ->descriptionIcon('heroicon-o-banknotes')
                ->color($saldo >= 0 ? 'info' : 'danger'),
        ];
    }
}