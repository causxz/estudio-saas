<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Plano Atual -->
        <x-filament::section>
            <x-slot name="heading">
                Status da Assinatura
            </x-slot>

            <div class="flex items-center gap-4">
                <div class="p-3 bg-primary-500/10 rounded-lg">
                    <x-filament::icon
                        icon="heroicon-o-credit-card"
                        class="w-8 h-8 text-primary-500"
                    />
                </div>
                <div>
                    <h2 class="text-xl font-bold">
                        {{ $this->planType === 'plus' ? 'Plano Plus' : 'Plano Gratuito' }}
                    </h2>
                    <p class="text-sm text-gray-500">
                        {{ $this->studioName }}
                    </p>
                </div>
            </div>

            <div class="mt-6">
                @if($this->planType === 'plus')
                    <p class="text-success-600 font-semibold flex items-center gap-2">
                        <x-filament::icon icon="heroicon-o-check-circle" class="w-5 h-5"/>
                        Todos os limites liberados.
                    </p>
                @else
                    <p class="text-warning-600 font-semibold flex items-center gap-2">
                        <x-filament::icon icon="heroicon-o-exclamation-triangle" class="w-5 h-5"/>
                        Plano limitado a 1 profissional.
                    </p>
                    <p class="text-sm text-gray-500 mt-2">
                        Faça upgrade para adicionar até 5 profissionais e liberar automações de WhatsApp e Consultoria IA.
                    </p>
                @endif
            </div>
        </x-filament::section>

        <!-- Recursos -->
        <x-filament::section>
            <x-slot name="heading">
                Vantagens do Plano Plus
            </x-slot>

            <ul class="space-y-3">
                <li class="flex items-center gap-3">
                    <x-filament::icon icon="heroicon-o-check" class="w-5 h-5 text-success-500" />
                    <span>Até 5 Profissionais na Agenda</span>
                </li>
                <li class="flex items-center gap-3">
                    <x-filament::icon icon="heroicon-o-check" class="w-5 h-5 text-success-500" />
                    <span>Automação de Lembretes via WhatsApp</span>
                </li>
                <li class="flex items-center gap-3">
                    <x-filament::icon icon="heroicon-o-check" class="w-5 h-5 text-success-500" />
                    <span>Consultor de Marketing com Inteligência Artificial</span>
                </li>
                <li class="flex items-center gap-3">
                    <x-filament::icon icon="heroicon-o-check" class="w-5 h-5 text-success-500" />
                    <span>Suporte Prioritário</span>
                </li>
            </ul>
        </x-filament::section>
    </div>
</x-filament-panels::page>
