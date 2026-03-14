<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Carbon\Carbon;
use Filament\Support\Icons\Heroicon;
// use Filament\Notifications\Notification;
// use Illuminate\Support\Facades\Http;
// use App\Models\Client;

class ConsultorMarketing extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;
    protected static ?string $navigationLabel = 'Consultor IA';
    protected static ?string $title = 'Consultor de Marketing (Gemini)';
    protected static ?int $navigationSort = 3;
    
    protected string $view = 'filament.pages.consultor-marketing';

    public ?array $data = [];
    public ?string $respostaIa = null;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('objetivo')
                    ->label('Qual é o objetivo da campanha de hoje?')
                    ->options([
                        'recuperar' => 'Recuperar clientes sumidas (+ de 30 dias)',
                        'instagram' => 'Ideia de Post Educativo para o Instagram',
                        'outro' => 'Outro (Escrever o que desejo)',
                    ])
                    ->required()
                    ->live(),

                //opção "Outro"
                Textarea::make('prompt_customizado')
                    ->label('O que você deseja que a IA escreva?')
                    ->placeholder('Ex: Crie uma mensagem de feliz aniversário para enviar às clientes de março...')
                    ->visible(fn (Get $get) => $get('objetivo') === 'outro')
                    ->required(fn (Get $get) => $get('objetivo') === 'outro'),
            ])
            ->statePath('data');
    }

    public function gerarCampanha(): void
    {
        $estado = $this->form->getState();
        $objetivo = $estado['objetivo'] ?? null;
        $promptCustomizado = $estado['prompt_customizado'] ?? null;

        if (!$objetivo) return;

        $totalClientes = \App\Models\Client::count();
        $clientesSumidas = \App\Models\Client::whereDoesntHave('appointments', function ($q) {
            $q->where('starts_at', '>=', \Carbon\Carbon::now()->subDays(30));
        })->count();

        // Construção do Prompt para entregar texto limpo e direto
        $prompt = "Aja como um especialista em marketing para um estúdio de Extensão de Cílios. O estúdio tem {$totalClientes} clientes cadastradas, sendo {$clientesSumidas} que não voltam há 30 dias.\n\n";

        if ($objetivo === 'recuperar') {
            $prompt .= "Crie APENAS uma mensagem de WhatsApp persuasiva, educada e irresistível para enviar a essas {$clientesSumidas} clientes sumidas, oferecendo um pequeno mimo ou desconto para voltarem. Formate para WhatsApp (use * para negrito e emojis). Não coloque introdução nem informações dentro de [] para ajustes, me dê apenas a mensagem pronta para copiar e colar.";
        } elseif ($objetivo === 'instagram') {
            $prompt .= "Crie um roteiro ou texto para um post de Instagram focado em cuidados essenciais após fazer a extensão de cílios, para gerar autoridade.";
        } elseif ($objetivo === 'outro') {
            $prompt .= "Siga esta instrução exata para criar o conteúdo: " . $promptCustomizado . "\n\nMe dê apenas o texto final pronto para uso, sem introduções de 'Aqui está...'.";
        }

        $apiKey = env('GEMINI_API_KEY');
        if (empty($apiKey)) {
            $this->respostaIa = "Erro: Chave API não encontrada.";
            return;
        }

        // Chamada ao Gemini 2.5
        $response = \Illuminate\Support\Facades\Http::withoutVerifying()
            ->withHeaders(['Content-Type' => 'application/json', 'x-goog-api-key' => trim($apiKey)])
            ->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent', [
                'contents' => [['parts' => [['text' => $prompt]]]]
            ]);

        if ($response->successful()) {
            $this->respostaIa = $response->json('candidates.0.content.parts.0.text');
        } else {
            $this->respostaIa = "⚠️ ERRO DO GOOGLE: " . ($response->json('error.message') ?? $response->body());
        }
    }

    public function dispararWhatsApp(): void
    {
        $mensagemAEnviar = trim((string) $this->respostaIa);

        if (empty($mensagemAEnviar)) {
            \Filament\Notifications\Notification::make()->title('Aviso')->body('A mensagem está vazia.')->warning()->send();
            return;
        }

        $estado = $this->form->getState();
        $objetivo = $estado['objetivo'] ?? null;

        // Filtro de clientes baseado no objetivo
        $query = \App\Models\Client::query();
        if ($objetivo === 'recuperar') {
            $query->whereDoesntHave('appointments', function ($q) {
                $q->where('starts_at', '>=', \Carbon\Carbon::now()->subDays(30));
            });
        }
        $clientesAlvo = $query->get();

        if ($clientesAlvo->isEmpty()) {
            \Filament\Notifications\Notification::make()->title('Ops!')->body('Nenhuma cliente encontrada para este filtro.')->warning()->send();
            return;
        }

        $sucessos = 0;

        foreach ($clientesAlvo as $cliente) {

            $telefoneRaw = $cliente->whatsapp ?? '';
            $numeroLimpo = preg_replace('/[^0-9]/', '', $telefoneRaw);
            
            if (empty($numeroLimpo) || strlen($numeroLimpo) < 10) continue;

            if (strlen($numeroLimpo) <= 11) {
                $numeroLimpo = '55' . $numeroLimpo;
            }

            // Disparo sem debug
            \Illuminate\Support\Facades\Http::withHeaders([
                'apikey' => 'ChaveSecretaEstudio123',
                'Content-Type' => 'application/json'
            ])->post("http://localhost:8080/message/sendText/estudio", [
                'number' => $numeroLimpo,
                'text' => $mensagemAEnviar,
                'textMessage' => [
                    'text' => $mensagemAEnviar
                ],
                'options' => [
                    'delay' => 1800, //delay de 1.8s
                    'presence' => 'composing'
                ]
            ]);

            $sucessos++;
            
            sleep(3); //intervalo de 3s para evitar ban
        }

        \Filament\Notifications\Notification::make()
            ->title('Campanha Finalizada!')
            ->body("Mensagem enviada com sucesso para {$sucessos} clientes.")
            ->success()
            ->send();

        $this->respostaIa = null;
    }
}