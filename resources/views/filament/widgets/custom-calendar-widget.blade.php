<x-filament-widgets::widget>
    <x-filament::section class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800">

        <div class="flex flex-col md:flex-row items-center justify-between mb-6 border-b border-gray-100 dark:border-gray-800 pb-6 gap-4 md:gap-0">
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">Minha Agenda</h2>
            <div>
                {{ $this->createAppointmentAction }}
            </div>
        </div>

        <style>
            /* INTEGRAÇÃO COM O TEMA DO FILAMENT (Stone/Marrom) */
            .fc {
                font-family: inherit;
                /* Variáveis do FullCalendar ligadas às variáveis do Tailwind/Filament */
                --fc-border-color: var(--fi-color-gray-200);
                /* Bordas suaves no claro */
                --fc-page-bg-color: transparent;
                --fc-neutral-bg-color: var(--fi-color-gray-50);
                /* Fundo de cabeçalhos */
                --fc-list-event-hover-bg-color: var(--fi-color-gray-100);
                --fc-today-bg-color: transparent !important;
                
            }

            /* Tema Escuro (Injetado via Tailwind dark class nas variáveis) */
            .dark .fc {
                --fc-border-color: var(--fi-color-gray-800);
                --fc-neutral-bg-color: var(--fi-color-gray-800);
            }

            /* Respiro para o calendário no desktop */
            #meu-calendario {
                padding-top: 10px;
            }


            .fc-timegrid-now-indicator-line,
            .fc-timegrid-now-indicator-arrow {
                display: none !important;
            }

            /* Estilização dos Cabeçalhos das Colunas */
            .fc-theme-standard th {
                border-color: var(--fc-border-color) !important;
            }

            .fc-col-header-cell {
                padding: 12px 0 !important;
            }

            .fc-col-header-cell-cushion {
                color: var(--fi-color-gray-500);
                font-size: 0.75rem;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                text-decoration: none;
            }

            .dark .fc-col-header-cell-cushion {
                color: var(--fi-color-gray-400);
            }

            /* Estilização dos Eventos (Agendamentos) */
            .fc-event {
                border-radius: 6px !important;
                padding: 2px 4px !important;
                transition: transform 0.2s ease, box-shadow 0.2s ease;
                margin: 1px !important;
                border: none !important;
                box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            }

            .fc-event:hover {
                transform: translateY(-1px);
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
                cursor: pointer;
                z-index: 50;
            }

            /* ATIVAÇÃO DAS LINHAS DE GRADE (Verticais e Horizontais) */
            .dark .fc-theme-standard td,
            .dark .fc-theme-standard th,
            .dark .fc-theme-standard .fc-scrollgrid {
                border: 1px solid #ffffff !important;
            }

            /* ATIVAÇÃO DAS LINHAS DE GRADE NO CLARO */
            .fc-theme-standard td,
            .fc-theme-standard th,
            .fc-theme-standard .fc-scrollgrid {
                border: 1px solid #313131 !important;
            }

            /*  CORREÇÃO DA QUEBRA DE TEXTO NO MOBILE E DESKTOP */
            .fc-event-main {
                font-weight: 500;
                font-size: 0.75rem;
                /* Força o texto a ficar em uma linha e adiciona "..." no final se for muito grande */
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap !important;
                line-height: 1.4;
            }

            /* Estilização do Relógio Lateral */
            .fc-timegrid-slot-label-cushion {
                font-size: 0.75rem;
                color: var(--fi-color-gray-400);
                font-weight: 500;
            }

            .fc-timegrid-slots td {
                height: 3.5em !important;
            }

            /* Altura agradável para clique */

            /* Título do Mês (Botão Superior) */
            .fc-toolbar-title {
                font-size: 1.125rem !important;
                font-weight: 700 !important;
                color: var(--fi-color-gray-800) !important;
            }

            .dark .fc-toolbar-title {
                color: var(--fi-color-gray-100) !important;
            }

            /* Botões do Calendário (Hoje, Mês, Semana) */
            .fc .fc-button-primary {
                background-color: transparent !important;
                color: var(--fi-color-gray-600) !important;
                border: 1px solid var(--fi-color-gray-200) !important;
                border-radius: 8px !important;
                /* Menos arredondado, mais SaaS */
                font-size: 0.8rem !important;
                font-weight: 500 !important;
                text-transform: capitalize !important;
                padding: 0.4rem 1rem !important;
                margin: 0 2px !important;
                transition: all 0.2s;
            }

            .dark .fc .fc-button-primary {
                color: var(--fi-color-gray-300) !important;
                border-color: var(--fi-color-gray-700) !important;
            }

            .fc .fc-button-primary:hover {
                background-color: var(--fi-color-gray-50) !important;
                color: var(--fi-color-gray-900) !important;
            }

            .dark .fc .fc-button-primary:hover {
                background-color: var(--fi-color-gray-800) !important;
                color: white !important;
            }

            .fc .fc-button-active {
                background-color: var(--fi-color-gray-100) !important;
                color: var(--fi-color-gray-900) !important;
                box-shadow: none !important;
            }

            .dark .fc .fc-button-active {
                background-color: var(--fi-color-gray-700) !important;
                color: white !important;
            }

            /* --- RESPONSIVIDADE EXTREMA PARA O CELULAR --- */
            @media (max-width: 768px) {
                .fc-toolbar {
                    flex-direction: column;
                    gap: 12px;
                }

                .fc-toolbar-title {
                    font-size: 1rem !important;
                }

                /* Força os dias do Month View a terem altura mínima sem estourar */
                .fc-daygrid-day-frame {
                    min-height: 80px !important;
                    overflow: hidden !important;
                }

                /* Garante que os eventos no mobile NUNCA quebrem a linha */
                .fc-event-main {
                    white-space: nowrap !important;
                    font-size: 0.7rem;
                    /* Levemente menor no celular para caber mais */
                }

                .fc-header-toolbar .fc-toolbar-chunk {
                    display: flex;
                    justify-content: center;
                    flex-wrap: wrap;
                    gap: 4px;
                }
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
                            nowIndicator: false, 
                            slotMinTime: '07:00:00',
                            slotMaxTime: '21:00:00',
                            slotDuration: '00:30:00',
                            slotLabelInterval: '01:00', 
                            allDaySlot: false,
                            editable: true, 
                            eventOverlap: false, 
                            events: eventosDoBanco,
                            
                            // Formatação do Evento: Mostra apenas Hora + Título, limpando sujeira visual
                            eventTimeFormat: {
                                hour: '2-digit',
                                minute: '2-digit',
                                meridiem: false
                            },
                            displayEventEnd: true, /* Mostra '10:00 - 11:30' se houver espaço */

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
                                week: 'Semana',
                                day: 'Dia'
                            },

                            eventDrop: (info) => {
                                $wire.updateAppointmentDates(info.event.id, info.event.startStr, info.event.endStr || info.event.startStr);
                            },
                            eventResize: (info) => {
                                $wire.updateAppointmentDates(info.event.id, info.event.startStr, info.event.endStr || info.event.startStr);
                            },
                            
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