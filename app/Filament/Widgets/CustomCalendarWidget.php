<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Appointment;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Schemas\Schema;
use App\Filament\Resources\Appointments\Schemas\AppointmentForm;

class CustomCalendarWidget extends Widget implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;

    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    protected string $view = 'filament.widgets.custom-calendar-widget';
    public array $events = [];

    public function mount(): void
    {
        $this->carregarAgendamentos();
    }

    // --- 1. AÇÃO: CRIAR NOVO AGENDAMENTO VIA BOTÃO (+) ---
    public function createAppointmentAction(): \Filament\Actions\Action
    {
        return CreateAction::make('createAppointment')
            ->label('+')
            ->model(Appointment::class)
            ->form(AppointmentForm::configure(new Schema())->getComponents())
            ->mutateFormDataUsing(function (array $data): array {
                $data['studio_id'] = Filament::getTenant()->id; // SEGURANÇA NA CRIAÇÃO
                return $data;
            })
            ->after(function () {
                $this->carregarAgendamentos(); // Atualiza a agenda imediatamente
            })
            ->successNotificationTitle('Agendamento salvo na agenda!');
    }

    // --- 2. AÇÃO: EDITAR AO CLICAR NO EVENTO ---
    public function editAppointmentAction(): \Filament\Actions\Action
    {
        return EditAction::make('editAppointment')
            ->record(fn(array $arguments) => Appointment::where('studio_id', Filament::getTenant()->id)->find($arguments['record'])) // SEGURANÇA NA EDIÇÃO
            ->form(AppointmentForm::configure(new Schema())->getComponents())
            ->after(function () {
                $this->carregarAgendamentos(); // Atualiza a agenda imediatamente
            })
            ->successNotificationTitle('Agendamento atualizado com sucesso!');
    }

    // --- 3. CARREGAR EVENTOS DO BANCO ---
    public function carregarAgendamentos(): void
    {
        $studioId = Filament::getTenant()->id;

        // SEGURANÇA: Filtra a busca do banco de dados travando no estúdio
        $this->events = Appointment::with(['client', 'service'])
            ->where('studio_id', $studioId)
            ->get()
            ->map(function ($appointment) {
                $cores = $this->obterCoresPastel($appointment->status ?? '');

                return [
                    'id' => $appointment->id,
                    'title' => ($appointment->client->name ?? 'Sem Cliente') . ' - ' . ($appointment->service->name ?? 'Sem Serviço'),
                    'start' => $appointment->starts_at,
                    'end' => $appointment->ends_at,
                    'backgroundColor' => $cores['bg'],
                    'borderColor' => $cores['border'],
                    'textColor' => $cores['text'],
                ];
            })
            ->toArray();
    }

    // --- 4. ARRASTAR E SOLTAR (DRAG AND DROP) ---
    public function updateAppointmentDates($id, $newStart, $newEnd): void
    {
        $studioId = Filament::getTenant()->id;

        // SEGURANÇA: Busca o agendamento garantindo que ele PERTENCE ao estúdio logado
        $appointment = Appointment::where('studio_id', $studioId)->find($id);

        if ($appointment) {
            $appointment->starts_at = Carbon::parse($newStart)->format('Y-m-d H:i:s');
            $appointment->ends_at = Carbon::parse($newEnd)->format('Y-m-d H:i:s');
            $appointment->save();

            $this->carregarAgendamentos();
        }
    }

    private function obterCoresPastel(string $status): array
    {
        return match ($status) {
            'agendado' =>   ['bg' => '#fef3c7', 'border' => '#fde68a', 'text' => '#b45309'], // Âmbar Suave
            'concluido' => ['bg' => '#d1fae5', 'border' => '#a7f3d0', 'text' => '#047857'], // Esmeralda Suave
            'cancelado' =>  ['bg' => '#fee2e2', 'border' => '#fecaca', 'text' => '#b91c1c'], // Rosa/Vermelho Suave
            default =>      ['bg' => '#f1f5f9', 'border' => '#e2e8f0', 'text' => '#475569'], // Cinza Suave
        };
    }
}
