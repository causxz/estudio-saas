<?php

namespace App\Filament\Billing;

use Closure;
use Filament\Billing\Providers\Contracts\BillingProvider;
use Illuminate\Http\RedirectResponse;

class AsaasBillingProvider implements BillingProvider
{
    public function getRouteAction(): string | Closure | array
    {
        return function (): RedirectResponse {
            return redirect()->route('filament.admin.tenant.pages.meu-plano');
        };
    }

    public function getSubscribedMiddleware(): string
    {
        // Se quisermos bloquear o acesso global, colocamos middleware aqui
        // Como o acesso gratuito é permitido, retornaremos algo pass-through (nulo não é permitido, então podemos retornar um dummy ou middleware existente pass-through)
        return \Illuminate\Routing\Middleware\SubstituteBindings::class;
    }
}
