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

        foreach ($agendamentos as $agendamento) {
            $this->enviarWhatsApp($agendamento);
        }

        $this->info('Lembretes verificados e enviados: ' . $agendamentos->count());
    }

    private function enviarWhatsApp($agendamento)
    {
        $cliente = $agendamento->client;
        $local = $agendamento->location;
        $dataHora = Carbon::parse($agendamento->starts_at);
        
        $nomeCliente = explode(' ', trim($cliente->name))[0];
        
        // Monta a mensagem dinâmica usando o banco de dados
        $msg = "Olá, *{$nomeCliente}*! ✨\n\nEste é um lembrete automático do seu agendamento de *{$agendamento->service->name}* para amanhã!\n\n📅 *Data:* {$dataHora->format('d/m/Y')}\n⏰ *Horário:* {$dataHora->format('H:i')}\n📍 *Local:* {$local->name}\n📌 *Endereço:* {$local->address}";
        
        if ($local->maps_link) {
            $msg .= "\n🗺️ *Google Maps:* {$local->maps_link}";
        }

        $msg .= "\n\nPor favor, responda com *SIM* para confirmar ou *NÃO* para reagendar. 💕";

        $numero = preg_replace('/[^0-9]/', '', $cliente->whatsapp);
        if (strlen($numero) <= 11) $numero = '55' . $numero;

        Http::withHeaders([
            'apikey' => 'ChaveSecretaEstudio123',
            'Content-Type' => 'application/json'
        ])->post("http://localhost:8080/message/sendText/estudio", [
            'number' => $numero,
            'text' => $msg
        ]);
        
        sleep(3); // Antiban
    }
}