<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Carbon;
use App\Models\Studio;
use App\Models\Client;
use App\Models\Service;
use App\Models\Professional;
use App\Models\Appointment;
use App\Models\Transaction;

class StudioTestDataSeeder extends Seeder
{
    public function run(): void
    {
        $studioId = 1;

        // Ensure the studio exists, otherwise the seeder might fail
        $studio = Studio::find($studioId);
        if (!$studio) {
            $this->command->error("Estúdio com ID {$studioId} não encontrado. Crie um estúdio primeiro.");
            return;
        }

        $this->command->info("Criando dados de teste para o Estúdio: {$studio->name} (ID: {$studioId})");

        // 1. Serviços
        $service1 = Service::create([
            'studio_id' => $studioId,
            'name' => 'Extensão de Cílios Fio a Fio',
            'price' => 150.00,
            'duration_minutes' => 120,
            'commission_percentage' => 40.00,
        ]);

        $service2 = Service::create([
            'studio_id' => $studioId,
            'name' => 'Volume Russo',
            'price' => 200.00,
            'duration_minutes' => 150,
            'commission_percentage' => 45.00,
        ]);

        $service3 = Service::create([
            'studio_id' => $studioId,
            'name' => 'Manutenção Fio a Fio',
            'price' => 80.00,
            'duration_minutes' => 60,
            'commission_percentage' => 40.00,
        ]);

        // 2. Profissionais (A dona já deve existir, mas vamos garantir 2 a mais)
        $prof1 = Professional::firstOrCreate(
            ['studio_id' => $studioId, 'name' => 'Ana Silva (Lash)'],
            ['phone' => '11999999999']
        );

        $prof2 = Professional::firstOrCreate(
            ['studio_id' => $studioId, 'name' => 'Beatriz Costa (Unhas)'],
            ['phone' => '11988888888']
        );

        // 3. Clientes
        $clients = [];
        $nomesClientes = ['Camila Ferreira', 'Juliana Santos', 'Mariana Rocha', 'Fernanda Lima', 'Patrícia Gomes'];
        foreach ($nomesClientes as $index => $nome) {
            $clients[] = Client::create([
                'studio_id' => $studioId,
                'name' => $nome,
                'whatsapp' => '1197777777' . $index,
                'birth_date' => Carbon::now()->subYears(20 + $index)->format('Y-m-d'),
                'preferences_summary' => 'Gosta de cílios mais naturais. ' . $nome,
            ]);
        }

        // 4. Agendamentos e Transações (Mês Atual, Semana Passada e Próximos Dias)
        $now = Carbon::now();
        
        $cenariosAgendamento = [
            // [Dias de diferença a partir de hoje, Cliente, Servico, Profissional, Status]
            [-5, $clients[0], $service1, $prof1, 'completed'],
            [-3, $clients[1], $service2, $prof1, 'completed'],
            [-1, $clients[2], $service3, $prof2, 'no_show'],
            [0,  $clients[3], $service1, $prof1, 'confirmed'],
            [1,  $clients[4], $service2, $prof2, 'scheduled'],
            [3,  $clients[0], $service3, $prof1, 'scheduled'],
        ];

        foreach ($cenariosAgendamento as $cenario) {
            $dias = $cenario[0];
            $cliente = $cenario[1];
            $servico = $cenario[2];
            $profissional = $cenario[3];
            $status = $cenario[4];

            $dataAgendamento = $now->copy()->addDays($dias)->setHour(rand(9, 17))->setMinute(0);

            $appointment = Appointment::create([
                'studio_id' => $studioId,
                'client_id' => $cliente->id,
                'service_id' => $servico->id,
                'professional_id' => $profissional->id,
                'starts_at' => $dataAgendamento,
                'ends_at' => $dataAgendamento->copy()->addMinutes($servico->duration_minutes),
                'status' => $status,
                'notes' => 'Agendamento gerado por seeder.',
            ]);

            // Se for completado, gera uma transação financeira de entrada
            if ($status === 'completed') {
                Transaction::create([
                    'studio_id' => $studioId,
                    'appointment_id' => $appointment->id,
                    'professional_id' => $profissional->id,
                    'type' => 'entrada',
                    'amount' => $servico->price,
                    'description' => 'Pagamento - ' . $servico->name,
                    'payment_method' => 'pix',
                    'transaction_date' => $dataAgendamento,
                    'notes' => 'Transação gerada pelo seeder.',
                ]);
            }
        }

        $this->command->info('✅ Carga de testes concluída para o Estúdio 1!');
    }
}
