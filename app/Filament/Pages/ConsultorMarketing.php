<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Carbon\Carbon;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Http;
use App\Models\Client;

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
                        'promocao' => 'Mensagem de Promoção Rápida (Preencher agenda)',
                        'instagram' => 'Ideia de Post Educativo para o Instagram',
                    ])
                    ->required(),
            ])
            ->statePath('data');
    }

    public function gerarCampanha(): void
    {
        $estado = $this->form->getState();
        $objetivo = $estado['objetivo'] ?? null;

        if (!$objetivo) return;

        $totalClientes = \App\Models\Client::count();
        $clientesSumidas = \App\Models\Client::whereDoesntHave('appointments', function ($q) {
            $q->where('starts_at', '>=', \Carbon\Carbon::now()->subDays(30));
        })->count();

        $prompt = "Aja como um especialista em marketing para um estúdio de Extensão de Cílios. O estúdio tem {$totalClientes} clientes cadastradas, sendo {$clientesSumidas} que não voltam há 30 dias. Objetivo: {$objetivo}. ";

        if ($objetivo === 'recuperar') {
            $prompt .= "Crie uma mensagem de WhatsApp persuasiva, educada e irresistível para enviar a essas {$clientesSumidas} clientes sumidas, oferecendo um pequeno mimo ou desconto para voltarem.";
        } elseif ($objetivo === 'promocao') {
            $prompt .= "Crie um texto curto e magnético para enviar no WhatsApp e Stories do Instagram, anunciando que restam poucas vagas para esta semana e fazendo um apelo à ação.";
        } else {
            $prompt .= "Crie um roteiro ou texto para um post de Instagram focado em cuidados essenciais após fazer a extensão de cílios, para gerar autoridade.";
        }

        $apiKey = env('GEMINI_API_KEY');
        
        if (empty($apiKey)) {
            $this->respostaIa = "Erro Interno: A chave API não foi encontrada no ficheiro .env.";
            return;
        }

        // 1. URL limpa, sem a chave misturada
        // 2. Chave passada no Header oficial (x-goog-api-key) com trim() para limpar espaços
        // 3. Estrutura de conteúdo idêntica à documentação oficial
        $response = \Illuminate\Support\Facades\Http::withoutVerifying()
            ->withHeaders([
                'Content-Type' => 'application/json',
                'x-goog-api-key' => trim($apiKey)
            ])
            ->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent', [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ]
            ]);

        if ($response->successful()) {
            $this->respostaIa = $response->json('candidates.0.content.parts.0.text');
        } else {
            $erroDoGoogle = $response->json('error.message') ?? $response->body();
            $this->respostaIa = "⚠️ ERRO DO GOOGLE: " . $erroDoGoogle;
        }
    }
}