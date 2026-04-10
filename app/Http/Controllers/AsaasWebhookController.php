<?php

namespace App\Http\Controllers;

use App\Models\Studio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache; 

class AsaasWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // 1. Validar o Token de Segurança do Asaas definido no .env
        $token = $request->header('asaas-access-token');
        if ($token !== config('services.asaas.webhook_token')) {
            Log::warning('Tentativa de Webhook Asaas com token inválido.', ['ip' => $request->ip()]);
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // 2. Proteção Anti-Replay Attack (Verifica se o evento já foi processado)
        // O Asaas envia um 'id' único para cada disparo de webhook (ex: evt_000000000001)
        $eventId = $request->input('id'); 
        
        if (!$eventId) {
            return response()->json(['error' => 'Event ID missing'], 400);
        }

        // Se este ID já estiver no nosso Cache, ignoramos e devolvemos sucesso para o Asaas parar de enviar
        if (Cache::has("webhook_{$eventId}")) {
            Log::info("Webhook Asaas ignorado (Replay Attack / Já processado): {$eventId}");
            return response()->json(['message' => 'Evento já processado'], 200);
        }

        // 3. Coletar os dados principais
        $event = $request->input('event');
        $payment = $request->input('payment');
        $customerId = $payment['customer'] ?? null;

        Log::info("Webhook Asaas recebido: {$event} para o cliente {$customerId}");

        if (!$customerId) {
            return response()->json(['error' => 'Customer ID missing'], 400);
        }

        // 4. Encontrar o estúdio pelo ID do cliente no Asaas
        $studio = Studio::where('asaas_customer_id', $customerId)->first();

        if (!$studio) {
            Log::warning("Estúdio não encontrado para o Customer ID: {$customerId}");
            return response()->json(['error' => 'Studio not found'], 404);
        }

        // 5. Lógica baseada no evento
        switch ($event) {
            case 'PAYMENT_RECEIVED': // Pagamento de fatura avulsa (PIX/Cartão)
            case 'PAYMENT_CONFIRMED':
                $studio->update([
                    'status' => 'active',
                    'expires_at' => now()->addMonth(), // Dá 1 mês de acesso
                ]);
                break;

            case 'PAYMENT_OVERDUE': // Pagamento atrasado
                $studio->update(['status' => 'past_due']);
                break;

            case 'PAYMENT_DELETED': // Cobrança removida
                $studio->update(['status' => 'canceled']);
                break;
        }

        // 6. Marcar evento como processado no Cache por 24 horas (86400 segundos)
        // Isso tranca a porta para que este evento exato nunca mais seja executado
        Cache::put("webhook_{$eventId}", true, 86400);

        return response()->json(['success' => true]);
    }
}