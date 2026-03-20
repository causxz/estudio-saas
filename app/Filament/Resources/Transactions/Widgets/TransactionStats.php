<?php

namespace App\Filament\Resources\Transactions\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use App\Filament\Resources\Transactions\Pages\ListTransactions;
use Filament\Facades\Filament;

class TransactionStats extends StatsOverviewWidget
{
    // 1. Conecta os Cards à Tabela
    use InteractsWithPageTable;

    // 2. Avisa qual é a página da tabela que ele deve escutar
    protected function getTablePage(): string
    {
        return ListTransactions::class;
    }

    protected function getStats(): array
    {
        // 3. Pega o ID do estúdio logado atualmente (Blindagem Multi-Tenancy)
        $studioId = Filament::getTenant()->id;

        // 4. Pega a busca do banco de dados COM OS FILTROS DA TABELA JÁ APLICADOS
        $query = $this->getPageTableQuery();

        // 5. Faz as somas baseadas na busca filtrada E travadas no estúdio atual
        $entradas = (clone $query)
            ->where('studio_id', $studioId) // Trava de segurança
            ->where('type', 'entrada')
            ->sum('amount');
            
        $saidas = (clone $query)
            ->where('studio_id', $studioId) // Trava de segurança
            ->where('type', 'saida')
            ->sum('amount');
            
        $saldo = $entradas - $saidas;

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