<x-filament-panels::page>
    <div class="space-y-6">
        
        <div class="p-6 bg-white dark:bg-gray-900 rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10">
            <h2 class="text-lg font-bold mb-4">O que vamos criar hoje?</h2>
            
            <form wire:submit="gerarCampanha">
                {{ $this->form }}

                <div class="mt-6">
                    <x-filament::button type="submit" icon="heroicon-m-sparkles" color="primary">
                        Gerar com Inteligência Artificial
                    </x-filament::button>
                </div>
            </form>
        </div>

        @if($respostaIa)
            <div class="p-6 bg-primary-50 dark:bg-primary-900/20 rounded-xl ring-1 ring-primary-500/50 mt-6">
                <div class="flex items-center gap-2 mb-4 text-primary-600 dark:text-primary-400">
                    <x-heroicon-o-sparkles class="w-6 h-6" />
                    <h3 class="text-xl font-bold">Texto Gerado (Revise e edite se necessário):</h3>
                </div>
                
                <textarea 
                    wire:model="respostaIa" 
                    rows="8"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                ></textarea>
                
                <div class="mt-6 flex gap-4">
                    <x-filament::button wire:click="dispararWhatsApp" color="success" icon="heroicon-m-paper-airplane">
                        Disparar para Clientes via WhatsApp
                    </x-filament::button>
                </div>
            </div>
        @endif

    </div>
</x-filament-panels::page>