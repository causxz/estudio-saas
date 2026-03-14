<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class DispararLembretesAgendamento extends Command
{
    protected $signature = 'app:disparar-lembretes';
    protected $description = 'Dispara WhatsApp 24h antes do agendamento';

    public function handle()
    {
        // Pega exatamente a janela de tempo de daqui a 24 horas (início e fim da hora atual + 24h)
        $inicioDaHora = Carbon::now()->addHours(24)->startOfHour();
        $fimDaHora = Carbon::now()->addHours(24)->endOfHour();

        $agendamentos = Appointment::with(['client', 'service', 'location'])
            ->where('status', 'agendado')
            ->whereBetween('starts_at', [$inicioDaHora, $fimDaHora])
            ->get();

        if ($agendamentos->isEmpty()) {
            $this->info('Nenhum agendamento encontrado para esta janela de horário.');
            return;
        }

        foreach ($agendamentos as $agendamento) {
            $this->enviarWhatsApp($agendamento);
        }

        $this->info("\n✅ Processo finalizado! Total processado: " . $agendamentos->count());
    }

    private function enviarWhatsApp($agendamento)
    {
        $cliente = $agendamento->client;
        $local = $agendamento->location;
        $dataHora = Carbon::parse($agendamento->starts_at);
        
        $nomeCliente = explode(' ', trim($cliente->name))[0];
        
        // Verifica se o local existe para evitar erros caso tenha sido apagado
        $nomeLocal = $local ? $local->name : 'Nosso Estúdio';
        $enderecoLocal = $local ? $local->address : 'Endereço não cadastrado';
        
        // Monta a mensagem dinâmica usando o banco de dados
        $msg = "Olá, *{$nomeCliente}*! ✨\n\nEste é um lembrete automático do seu agendamento de *{$agendamento->service->name}* para amanhã!\n\n📅 *Data:* {$dataHora->format('d/m/Y')}\n⏰ *Horário:* {$dataHora->format('H:i')}\n📍 *Local:* {$nomeLocal}\n📌 *Endereço:* {$enderecoLocal}";
        
        if ($local && $local->maps_link) {
            $msg .= "\n🗺️ *Google Maps:* {$local->maps_link}";
        }


        // Tratamento do número
        $numero = preg_replace('/[^0-9]/', '', $cliente->whatsapp);
        if (strlen($numero) <= 11) $numero = '55' . $numero;

        // Disparo com o Payload Blindado
        $response = Http::withHeaders([
            'apikey' => 'ChaveSecretaEstudio123',
            'Content-Type' => 'application/json'
        ])->post("http://localhost:8080/message/sendText/estudio", [
            'number' => $numero,
            'text' => $msg, // Formato v2
            'textMessage' => [
                'text' => $msg // Formato v1.6.1
            ],
            'options' => [
                'delay' => 1500,
                'presence' => 'composing'
            ]
        ]);
        
        // Feedback visual no Terminal
        if ($response->successful()) {
            $this->info("✔️  Enviado com sucesso para: {$nomeCliente} ({$numero})");
        } else {
            $erroDetalhe = $response->json('response.message') ?? $response->json('message') ?? $response->body();
            if (is_array($erroDetalhe)) {
                $erroDetalhe = json_encode($erroDetalhe, JSON_UNESCAPED_UNICODE);
            }
            $this->error("❌ Falha ao enviar para {$nomeCliente} ({$numero}): {$erroDetalhe}");
        }

        sleep(3); // Antiban
    }
}