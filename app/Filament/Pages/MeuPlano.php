<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class MeuPlano extends Page
{
    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-credit-card';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Configurações';
    }

    public function getTitle(): string | \Illuminate\Contracts\Support\Htmlable
    {
        return 'Meu Plano';
    }

    protected string $view = 'filament.pages.meu-plano';

    public $planType;
    public $studioName;

    public function mount()
    {
        $studio = \Filament\Facades\Filament::getTenant();
        $this->planType = $studio->plan_type ?? 'free';
        $this->studioName = $studio->name;
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('upgrade')
                ->label('Fazer Upgrade para Plus')
                ->color('primary')
                ->icon('heroicon-o-sparkles')
                ->visible(fn () => \Filament\Facades\Filament::getTenant()->plan_type !== 'plus')
                ->action(function () {
                    return $this->gerarLinkAsaas();
                }),
        ];
    }

    private function gerarLinkAsaas()
    {
        $studio = \Filament\Facades\Filament::getTenant();
        
        // Em um cenário real, você faria a chamada à API do Asaas aqui.
        // Simulando a criação de um link de pagamento:
        $apiKey = env('ASAAS_API_KEY');
        
        // Exemplo simplificado de chamada via HTTP Facade:
        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'access_token' => $apiKey,
        ])->post('https://sandbox.asaas.com/api/v3/paymentLinks', [
            'name' => 'Assinatura Plus - ' . $studio->name,
            'description' => 'Plano Plus Mensal',
            'chargeType' => 'RECURRENT',
            'endDateLimit' => null,
            'value' => 79.99,
            'billingType' => 'UNDEFINED',
            'subscriptionCycle' => 'MONTHLY',
        ]);

        if ($response->successful()) {
            $url = $response->json('url');
            return redirect()->away($url);
        }

        // Se falhar a API, você pode mostrar um erro:
        \Filament\Notifications\Notification::make()
            ->title('Erro ao gerar link de pagamento')
            ->body('Verifique as configurações do Asaas ou tente novamente mais tarde.')
            ->danger()
            ->send();
    }
}
