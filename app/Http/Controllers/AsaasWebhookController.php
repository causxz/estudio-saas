<?php

namespace App\Http\Controllers;

use App\Models\Studio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AsaasWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // 1. Validar o Token de Segurança do Asaas (Configura no teu .env)
        $token = $request->header('asaas-access-token');
        if ($token !== config('services.asaas.webhook_token')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $event = $request->input('event');
        $payment = $request->input('payment');
        $customerId = $payment['customer'] ?? null;

        Log::info("Webhook Asaas recebido: {$event} para o cliente {$customerId}");

        // 2. Encontrar o estúdio pelo ID do cliente no Asaas
        $studio = Studio::where('asaas_customer_id', $customerId)->first();

        if (!$studio) {
            Log::warning("Estúdio não encontrado para o Customer ID: {$customerId}");
            return response()->json(['error' => 'Studio not found'], 404);
        }

        // 3. Lógica baseada no evento
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

        return response()->json(['success' => true]);
    }
}