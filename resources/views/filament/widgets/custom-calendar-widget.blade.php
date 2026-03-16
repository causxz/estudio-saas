<x-filament-widgets::widget>
    <x-filament::section class="bg-white rounded-2xl shadow-sm border border-gray-100">

        <style>
            /* 1. O Fundo e as Linhas Delicadas */
            .fc { font-family: inherit; }
            .fc-theme-standard .fc-scrollgrid { border: none !important; }
            .fc-theme-standard td, .fc-theme-standard th { 
                border-color: #f8fafc !important; /* Linhas ultra claras (slate-50) */
            }

            /* 2. Cabeçalho dos Dias (Segunda, Terça...) */
            .fc-col-header-cell { 
                border-bottom: 1px solid #f1f5f9 !important; 
                padding: 16px 0 !important;
            }
            .fc-col-header-cell-cushion { 
                color: #64748b; /* slate-500 */
                font-size: 0.75rem; 
                font-weight: 500;
                text-transform: uppercase; 
                letter-spacing: 0.05em; 
            }

            /* 3. A Linha do Tempo Atual (Now Indicator) */
            .fc-timegrid-now-indicator-line { border-color: #f59e0b !important; border-width: 2px !important; }
            .fc-timegrid-now-indicator-arrow { 
                border: none !important; 
                background: #f59e0b !important; 
                width: 8px; height: 8px; 
                border-radius: 50%; 
                margin-top: -4px; 
            }

            /* 4. Estilização Elegante dos Agendamentos (Pílulas) */
            .fc-event {
                border-radius: 8px !important; /* Cantos arredondados */
                padding: 4px 6px !important;
                box-shadow: none !important;
                transition: all 0.2s ease;
                margin: 1px 2px !important;
            }
            .fc-event:hover {
                transform: translateY(-1px); /* Levanta suavemente ao passar o mouse */
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05) !important;
                cursor: pointer;
                z-index: 50;
            }
            .fc-event-main { 
                font-weight: 500; 
                font-size: 0.75rem; 
                overflow: hidden; 
                text-overflow: ellipsis; 
                white-space: nowrap; 
            }

            /* 5. Textos de Horário mais sutis */
            .fc-timegrid-slot-label-cushion { font-size: 0.75rem; color: #94a3b8; font-weight: 400; }
            .fc-timegrid-slots td { height: 3.5em !important; } /* Mais respiro entre os horários */

            /* 6. Botões Modernos e Arredondados */
            .fc-toolbar-title { font-size: 1.125rem !important; font-weight: 600 !important; color: #334155 !important; }
            .fc .fc-button-primary {
                background-color: transparent !important;
                color: #64748b !important;
                border: 1px solid #e2e8f0 !important;
                border-radius: 9999px !important; /* Formato de pílula total */
                font-size: 0.8rem !important;
                text-transform: capitalize !important;
                padding: 0.4rem 1rem !important;
                transition: all 0.2s;
            }
            .fc .fc-button-primary:hover { background-color: #f8fafc !important; color: #0f172a !important; }
            .fc .fc-button-active { background-color: #f1f5f9 !important; border-color: #cbd5e1 !important; color: #0f172a !important; box-shadow: none !important; }

            /* RESPONSIVIDADE PARA CELULAR */
            @media (max-width: 768px) {
                .fc-toolbar { flex-direction: column; gap: 12px; }
                .fc-toolbar-title { font-size: 1rem !important; }
                .fc-event-main { white-space: normal; line-height: 1.2; }
                
                /* AJUSTE NOVO: Garante que os dias no modo Mês não fiquem espremidos no celular */
                .fc-daygrid-day-frame { min-height: 70px !important; }
            }
        </style>

        <div wire:ignore>
            <div id="meu-calendario" class="mt-2"
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
                        
                        // Trava de Celular
                        var isMobile = window.innerWidth < 768;

                        var calendar = new FullCalendar.Calendar(calendarEl, {
                            // Continua abrindo no DIA por padrão no celular para carregar mais limpo
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
                            
                            // AJUSTE: O menu de celular agora tem 'dayGridMonth' (Mês) e 'timeGridDay' (Dia)
                            headerToolbar: isMobile ? {
                                left: 'prev,next',
                                center: 'title',
                                right: 'dayGridMonth,timeGridDay'
                            } : {
                                left: 'prev,next today',
                                center: 'title',
                                right: 'dayGridMonth,timeGridWeek,timeGridDay'
                            },

                            // A função windowResize foi completamente removida para não atrapalhar!
                            
                            eventDrop: (info) => {
                                $wire.updateAppointmentDates(info.event.id, info.event.startStr, info.event.endStr || info.event.startStr);
                            },
                            
                            eventResize: (info) => {
                                $wire.updateAppointmentDates(info.event.id, info.event.startStr, info.event.endStr || info.event.startStr);
                            }
                        });
                        calendar.render();
                    }
                }"></div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>