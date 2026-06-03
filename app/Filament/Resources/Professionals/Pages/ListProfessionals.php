<?php

namespace App\Filament\Resources\Professionals\Pages;

use App\Filament\Resources\Professionals\ProfessionalResource;
use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use Filament\Forms\Components\Toggle; 
use Filament\Resources\Pages\ListRecords;
use Filament\Facades\Filament;

class ListProfessionals extends ListRecords
{
    protected static string $resource = ProfessionalResource::class;

    protected function getHeaderActions(): array
    {
        return [

            Action::make('configurar_comissoes')
                ->label('Configurar Comissões')
                ->icon('heroicon-o-cog-6-tooth')
                ->color('warning') // Fica amarelo para chamar atenção
                ->visible(fn () => Filament::getTenant()->plan_type === 'plus')
                ->form([
                    Toggle::make('has_commissions')
                        ->label('Sistema de Comissões')
                        ->helperText('Ligue para liberar as funções financeiras de repasse na agenda e nos serviços.')
                        // Puxa inteligentemente se já está ligado ou não no banco de dados
                        ->default(fn() => Filament::getTenant()->has_commissions),
                ])
                ->action(function (array $data) {
                    // Salva a escolha direto no banco de dados
                    $studio = Filament::getTenant();
                    $studio->update([
                        'has_commissions' => $data['has_commissions'],
                    ]);
                })
                ->modalWidth('sm')
                ->modalHeading('Configurações do Estúdio')
                ->modalDescription('Gerencie o sistema de comissões da sua equipe.'),

            CreateAction::make()
                ->modalWidth('md')
                ->label('Novo Profissional')
                ->icon('heroicon-o-plus')
                ->before(function (CreateAction $action) {
                    $studio = Filament::getTenant();
                    if ($studio->plan_type !== 'plus' && $studio->professionals()->count() >= 1) {
                        \Filament\Notifications\Notification::make()
                            ->danger()
                            ->title('Limite Atingido')
                            ->body('Faça upgrade para o plano Plus para adicionar mais profissionais.')
                            ->send();
                        $action->halt();
                    }
                }),
        ];
    }
}
