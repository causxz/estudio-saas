<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use App\Filament\Resources\Appointments\AppointmentResource;

class CalendarWidget extends FullCalendarWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    public function config(): array
    {
        return [
            'initialView' => 'timeGridWeek',
            'headerToolbar' => [
                'left' => 'prev,next today',
                'center' => 'title',
                'right' => 'dayGridMonth,timeGridWeek,timeGridDay',
            ],
            'slotMinTime' => '07:00:00',
            'slotMaxTime' => '21:00:00',
            'allDaySlot' => false,
        ];
    }

    public function fetchEvents(array $fetchInfo): array
    {
        return Appointment::query()
            ->with(['client', 'service'])
            ->where('starts_at', '>=', $fetchInfo['start'])
            ->where('ends_at', '<=', $fetchInfo['end'])
            ->get()
            ->map(function ($appointment) {
                // Ensinamos ao VS Code exatamente o que é esta variável
                /** @var \App\Models\Appointment $appointment */
                
                // Usamos uma Array simples em vez da classe EventData
                return [
                    'id' => $appointment->id,
                    'title' => $appointment->client->name . ' - ' . $appointment->service->name,
                    'start' => $appointment->starts_at,
                    'end' => $appointment->ends_at,
                    'url' => AppointmentResource::getUrl('edit', ['record' => $appointment->id]),
                    'color' => $this->obterCorPorStatus($appointment->status ?? ''),
                ];
            })
            ->toArray();
    }

    private function obterCorPorStatus(string $status): string
    {
        return match ($status) {
            'agendado' => '#f59e0b',   // Amarelo
            'confirmado' => '#10b981', // Verde
            'concluido' => '#3b82f6',  // Azul
            'cancelado' => '#ef4444',  // Vermelho
            default => '#6b7280',      // Cinza
        };
    }
}