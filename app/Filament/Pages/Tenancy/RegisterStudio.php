<?php

namespace App\Filament\Pages\Tenancy;

use App\Models\Studio;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Pages\Tenancy\RegisterTenant;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RegisterStudio extends RegisterTenant
{
    public static function getLabel(): string
    {
        return 'Configurar Meu Estúdio';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nome do Estúdio')
                    ->placeholder('Ex: Louyse Lash Design')
                    ->required()
                    ->maxLength(255),
                    
                Select::make('plan_type')
                    ->label('Escolha seu Plano')
                    ->options([
                        'iniciante' => 'Iniciante (R$ 29/mês)',
                        'professional' => 'Professional (R$ 79/mês)',
                        'business' => 'Elite Business - Com IA (R$ 149/mês)',
                    ])
                    ->required()
                    ->default('professional'),

                Toggle::make('has_commissions')
                    ->label('Ativar Sistema de Comissões')
                    ->helperText('Ligue se o seu estúdio possui profissionais que ganham por comissão.')
                    ->default(false),
            ]);
    }

    protected function handleRegistration(array $data): Studio
    {
        $data['slug'] = Str::slug($data['name']);
        $user = auth()->user();

        // --- 1. INTEGRAÇÃO ASAAS (Antes de salvar no banco local) --- //
        try {
            // Cria o Cliente no Asaas
            $response = Http::withHeaders([
                'access_token' => config('services.asaas.api_key'),
            ])->post('https://sandbox.asaas.com/api/v3/customers', [
                'name' => $user->name,
                'email' => $user->email,
                'company' => $data['name'],
            ]);

            if ($response->successful()) {
                $customerId = $response->json('id');
                $data['asaas_customer_id'] = $customerId;
                
                $valores = ['iniciante' => 29.00, 'professional' => 79.00, 'business' => 149.00];
                $valorPlano = $valores[$data['plan_type']] ?? 79.00;

                // Cria a Assinatura (Mensalidade) com 7 DIAS GRÁTIS
                $subResponse = Http::withHeaders([
                    'access_token' => config('services.asaas.api_key'),
                ])->post('https://sandbox.asaas.com/api/v3/subscriptions', [
                    'customer' => $customerId,
                    'billingType' => 'PIX',
                    'value' => $valorPlano,
                    'nextDueDate' => now()->addDays(7)->format('Y-m-d'), // Vence em 7 dias
                    'cycle' => 'MONTHLY',
                    'description' => "Mensalidade Agenda Lash - Plano " . ucfirst($data['plan_type'])
                ]);
                
                if ($subResponse->successful()) {
                    $data['subscription_id'] = $subResponse->json('id');
                } else {
                    Log::error('Erro Asaas (Assinatura): ' . $subResponse->body());
                }
            } else {
                Log::error('Erro Asaas (Cliente): ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('Exceção na API do Asaas: ' . $e->getMessage());
        }

        // --- 2. PERSISTÊNCIA SEGURA NO BANCO (DB::transaction) --- //
        // Se qualquer linha aqui dentro der erro, o banco desfaz TUDO!
        return DB::transaction(function () use ($data, $user) {
            
            // Cria o estúdio no banco de dados
            $studio = Studio::create($data);

            // Vincula o estúdio à usuária que está a fazer o cadastro
            $studio->users()->attach($user);

            // Cria a dona do estúdio como a primeira "Profissional" automaticamente
            $studio->professionals()->create([
                'name' => $user->name,
            ]);

            return $studio;
        });
    }
}