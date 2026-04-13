<x-filament-widgets::widget>
    <x-filament::section class="bg-white rounded-2xl shadow-sm border border-gray-100">

        <div class="flex flex-col md:flex-row items-center justify-between mb-8 border-b border-gray-100 pb-6 gap-4 md:gap-0">
            <h2 class="text-xl font-bold text-gray-800">Minha Agenda</h2>
            <div>
                {{ $this->createAppointmentAction }}
            </div>
        </div>

        <style>
            .fc { font-family: inherit; }
            
            /* Respiro para o calendário no desktop */
            #meu-calendario { padding-top: 10px; }

            .fc-theme-standard .fc-scrollgrid { border: none !important; }
            .fc-theme-standard td, .fc-theme-standard th { border-color: #f8fafc !important; }
            .fc-col-header-cell { border-bottom: 1px solid #f1f5f9 !important; padding: 16px 0 !important; }
            .fc-col-header-cell-cushion { color: #64748b; font-size: 0.75rem; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em; }
            .fc-timegrid-now-indicator-line { border-color: #f59e0b !important; border-width: 2px !important; }
            .fc-timegrid-now-indicator-arrow { border: none !important; background: #f59e0b !important; width: 8px; height: 8px; border-radius: 50%; margin-top: -4px; }

            .fc-event { border-radius: 8px !important; padding: 4px 6px !important; transition: all 0.2s ease; margin: 1px 2px !important; border: none !important; }
            .fc-event:hover { transform: translateY(-1px); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05) !important; cursor: pointer; z-index: 50; }
            .fc-event-main { font-weight: 500; font-size: 0.75rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

            .fc-timegrid-slot-label-cushion { font-size: 0.75rem; color: #94a3b8; font-weight: 400; }
            .fc-timegrid-slots td { height: 3.5em !important; }

            .fc-toolbar-title { font-size: 1.125rem !important; font-weight: 600 !important; color: #334155 !important; }
            
            .fc .fc-button-primary {
                background-color: transparent !important;
                color: #64748b !important;
                border: 1px solid #e2e8f0 !important;
                border-radius: 9999px !important; 
                font-size: 0.8rem !important;
                text-transform: capitalize !important;
                padding: 0.4rem 1rem !important;
                margin: 0 2px !important;
                transition: all 0.2s;
            }
            .fc .fc-button-primary:hover { background-color: #f8fafc !important; color: #0f172a !important; }
            .fc .fc-button-active { background-color: #f1f5f9 !important; border-color: #cbd5e1 !important; color: #0f172a !important; box-shadow: none !important; }

            @media (max-width: 768px) {
                .fc-toolbar { flex-direction: column; gap: 12px; }
                .fc-toolbar-title { font-size: 1rem !important; }
                .fc-event-main { white-space: normal; line-height: 1.2; }
                .fc-daygrid-day-frame { min-height: 70px !important; }
                
                /* Ajuste para os botões não ficarem colados no mobile */
                .fc-header-toolbar .fc-toolbar-chunk { display: flex; justify-content: center; flex-wrap: wrap; gap: 4px; }
            }
        </style>

        <div wire:ignore>
            <div id="meu-calendario"
                x-data="{
                    init() {
                        if (typeof FullCalendar === 'undefined') {
                            let script = document.createElement('script');
                            script.src = 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js';
                            script.onload = () => this.montarCalendario();
                            document.head.appendChild(script);
                        } else {
                            this.montarCalendario();
                        }
                    },
                    montarCalendario() {
                        var calendarEl = document.getElementById('meu-calendario');
                        var eventosDoBanco = @js($this->events);
                        var isMobile = window.innerWidth < 768;

                        var calendar = new FullCalendar.Calendar(calendarEl, {
                            initialView: isMobile ? 'timeGridDay' : 'timeGridWeek',
                            locale: 'pt-br',
                            contentHeight: 'auto', 
                            expandRows: true,
                            nowIndicator: true, 
                            slotMinTime: '07:00:00',
                            slotMaxTime: '21:00:00',
                            slotDuration: '00:30:00',
                            slotLabelInterval: '01:00', 
                            allDaySlot: false,
                            editable: true, 
                            eventOverlap: false, 
                            events: eventosDoBanco,
                            
                            headerToolbar: {
                                left: 'prev,next today',
                                center: 'title',
                                right: isMobile 
                                    ? 'dayGridMonth,timeGridWeek,timeGridDay' 
                                    : 'dayGridMonth,timeGridWeek,timeGridDay'
                            },
                            
                            buttonText: {
                                today: 'Hoje',
                                month: 'Mês',
                                week: 'Sem.',
                                day: 'Dia'
                            },

                            eventDrop: (info) => {
                                $wire.updateAppointmentDates(info.event.id, info.event.startStr, info.event.endStr || info.event.startStr);
                            },
                            eventResize: (info) => {
                                $wire.updateAppointmentDates(info.event.id, info.event.startStr, info.event.endStr || info.event.startStr);
                            },
                            
                            // Edit Modal Action
                            eventClick: function(info) {
                                info.jsEvent.preventDefault();
                                $wire.mountAction('editAppointment', { record: info.event.id });
                            }
                        });
                        
                        calendar.render();

                        window.addEventListener('filament-action-closed', () => {
                             window.location.reload(); 
                        });
                    }
                }"></div>
        </div>
    </x-filament::section>

    <x-filament-actions::modals />
</x-filament-widgets::widget>