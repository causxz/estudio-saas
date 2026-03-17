<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Appointment;
use Carbon\Carbon;

class CustomCalendarWidget extends Widget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    protected string $view = 'filament.widgets.custom-calendar-widget';
    public array $events = [];

    public function mount(): void
    {
        $this->carregarAgendamentos();
    }

    public function carregarAgendamentos(): void
    {
        $this->events = Appointment::with(['client', 'service'])
            ->get()
            ->map(function ($appointment) {
                $cores = $this->obterCoresPastel($appointment->status ?? '');

                return [
                    'id' => $appointment->id,
                    'title' => $appointment->client->name . ' - ' . $appointment->service->name,
                    'start' => $appointment->starts_at,
                    'end' => $appointment->ends_at,
                    'backgroundColor' => $cores['bg'],
                    'borderColor' => $cores['border'],
                    'textColor' => $cores['text'],
                ];
            })
            ->toArray();
    }

    public function updateAppointmentDates($id, $newStart, $newEnd): void
    {
        $appointment = Appointment::find($id);

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
