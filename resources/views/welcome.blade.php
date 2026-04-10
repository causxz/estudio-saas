<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Agenda Lash | Gestão de Elite para Estúdios</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&family=Playfair+Display:ital,wght@0,700;1,700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

    <style>
        :root {
            /* Palette Terrosa */
            --bg-cream: #FDFBF7;
            --earth-dark: #452B1F;
            --earth-primary: #844D36;
            /* Terracota */
            --earth-accent: #C28E64;
            /* Dourado Soft / Bronze */
            --radius-pro: 24px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-cream);
            color: var(--earth-dark);
            overflow-x: hidden;
        }

        h1,
        h2,
        h3,
        .font-serif {
            font-family: 'Playfair Display', serif;
        }

        .glass-nav {
            background: rgba(253, 251, 247, 0.8);
            backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(69, 43, 31, 0.05);
        }

        .btn-earth {
            background-color: var(--earth-primary);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .btn-earth:hover {
            background-color: var(--earth-dark);
            transform: scale(1.05);
            box-shadow: 0 15px 30px rgba(69, 43, 31, 0.2);
        }

        .feature-card {
            background: #fff;
            border: 1px solid rgba(69, 43, 31, 0.03);
            transition: all 0.4s ease;
        }

        .feature-card:hover {
            border-color: var(--earth-accent);
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(69, 43, 31, 0.08);
        }

        .glow-effect {
            position: relative;
            overflow: hidden;
        }

        .glow-effect::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(194, 142, 100, 0.15), transparent);
            transform: rotate(45deg);
            transition: 0.8s;
        }

        .glow-effect:hover::after {
            left: 100%;
        }

        /* Novas animações interativas para os cards de planos */
        .plan-card {
            transition: box-shadow 0.4s ease, border-color 0.4s ease;
            transform-style: preserve-3d;
        }

        .plan-card:hover {
            box-shadow: 0 30px 60px rgba(69, 43, 31, 0.12);
            z-index: 10;
        }

        .plan-card svg {
            transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .plan-card:hover svg {
            transform: scale(1.3) rotate(5deg);
        }

        .plan-btn {
            transition: all 0.3s ease;
        }

        .plan-card:hover .plan-btn {
            transform: translateY(-3px);
        }
    </style>
</head>

<body class="antialiased">

    <nav class="glass-nav sticky top-0 z-50 py-4 px-6">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-2 reveal-top">
                <div class="w-10 h-10 bg-[var(--earth-primary)] rounded-full flex items-center justify-center text-white font-bold italic shadow-lg">AL</div>
                <span class="text-2xl font-bold tracking-tighter">Agenda<span class="text-[var(--earth-primary)]">Lash</span></span>
            </div>
            <div class="hidden md:flex items-center gap-10 text-sm font-semibold reveal-top">
                <a href="#features" class="hover:text-[var(--earth-primary)] transition">Funcionalidades</a>
                <a href="#precos" class="hover:text-[var(--earth-primary)] transition">Planos</a>
                @auth
                <a href="{{ url('/admin') }}" class="btn-earth text-white px-6 py-2.5 rounded-full">Meu Painel</a>
                @else
                <a href="#login-form" class="hover:text-[var(--earth-primary)] transition border border-transparent hover:border-[var(--earth-primary)] px-5 py-2 rounded-full">Acessar Sistema</a> @endauth
            </div>
        </div>
    </nav>

    <header class="relative pt-16 pb-32">
        <div class="max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-16 items-center">
            <div class="hero-text">
                <div class="inline-block py-1 px-4 rounded-full bg-[#844D3615] text-[var(--earth-primary)] text-xs font-bold uppercase tracking-widest mb-6">
                    Beleza com Inteligência
                </div>
                <h1 class="text-6xl md:text-7xl font-bold leading-[1.1] mb-8">
                    Organize seu estúdio com <span class="italic text-[var(--earth-primary)]">estilo e precisão.</span>
                </h1>
                <p class="text-xl text-stone-500 mb-10 leading-relaxed max-w-xl">
                    A primeira plataforma que une a arte do olhar com a ciência da gestão. Automatize lucros e conquiste a agenda dos sonhos.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('register') }}" class="btn-earth text-white px-10 py-5 rounded-full font-bold text-lg glow-effect">
                        Testar 7 Dias Grátis
                    </a>
                </div>
                <p class="text-sm text-stone-400 mt-6 flex items-center gap-2 italic">
                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" />
                    </svg>
                    Sk_test ativo via Asaas. Segurança total.
                </p>
            </div>

            <div class="hero-card">
                <div id="login-form" class="bg-white p-10 rounded-[var(--radius-pro)] shadow-[0_30px_100px_-20px_rgba(69,43,31,0.15)] border border-stone-50 relative">
                    <h2 class="text-2xl font-bold mb-8">Acessar Sistema</h2>
                    <form method="POST" action="{{ route('processar.login') }}" class="space-y-5"> @csrf
                        @error('email')
                        <div class="bg-red-50 text-red-600 text-sm p-3 rounded-xl font-medium border border-red-100">
                            {{ $message }}
                        </div>
                        @enderror

                        <div>
                            <label class="text-xs font-bold uppercase text-stone-400 block mb-2 ml-1">E-mail Profissional</label>
                            <input type="email" name="email" required class="w-full p-4 bg-stone-50 border-none rounded-2xl focus:ring-2 focus:ring-[var(--earth-accent)] outline-none" placeholder="ex: lash@estudio.com">
                        </div>
                        <div>
                            <label class="text-xs font-bold uppercase text-stone-400 block mb-2 ml-1">Senha</label>
                            <input type="password" name="password" required class="w-full p-4 bg-stone-50 border-none rounded-2xl focus:ring-2 focus:ring-[var(--earth-accent)] outline-none" placeholder="••••••••">
                        </div>
                        <button class="w-full btn-earth text-white py-4 rounded-2xl font-bold text-lg shadow-xl">
                            Entrar na Agenda
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <section id="features" class="py-32 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-24 reveal">
                <h2 class="text-4xl md:text-6xl font-bold mb-4">Tudo sob seu controle</h2>
                <p class="text-stone-400 text-lg">A tecnologia que trabalha enquanto você transforma olhares.</p>
                <div class="w-24 h-1 bg-[var(--earth-accent)] mx-auto mt-6"></div>
            </div>

            <div class="grid md:grid-cols-3 gap-12">
                <div class="feature-card p-10 rounded-[var(--radius-pro)] reveal">
                    <div class="w-14 h-14 bg-[#844D3610] rounded-2xl flex items-center justify-center mb-8 text-[var(--earth-primary)]">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Agenda Visual Pro</h3>
                    <p class="text-stone-500 leading-relaxed">Visão semanal por profissional com bloqueio inteligente de conflitos de horários.</p>
                </div>

                <div class="feature-card p-10 rounded-[var(--radius-pro)] reveal">
                    <div class="w-14 h-14 bg-[#844D3610] rounded-2xl flex items-center justify-center mb-8 text-[var(--earth-primary)]">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Comissões Automáticas</h3>
                    <p class="text-stone-500 leading-relaxed">Cálculo instantâneo de ganhos por profissional e controle de entradas e saídas de caixa.</p>
                </div>

                <div class="feature-card p-10 rounded-[var(--radius-pro)] reveal">
                    <div class="w-14 h-14 bg-[#844D3610] rounded-2xl flex items-center justify-center mb-8 text-[var(--earth-primary)]">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Anamnese Digital</h3>
                    <p class="text-stone-500 leading-relaxed">Fichas de saúde customizadas com suporte a assinatura digital coletada pelo celular da cliente.</p>
                </div>

                <div class="feature-card p-10 rounded-[var(--radius-pro)] reveal">
                    <div class="w-14 h-14 bg-[#844D3610] rounded-2xl flex items-center justify-center mb-8 text-[var(--earth-primary)]">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="1.5" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-4">WhatsApp Automático</h3>
                    <p class="text-stone-500 leading-relaxed">Redução de até 80% de faltas com disparos automáticos de confirmação e lembrete.</p>
                </div>

                <div class="feature-card p-10 rounded-[var(--radius-pro)] reveal">
                    <div class="w-14 h-14 bg-[#844D3610] rounded-2xl flex items-center justify-center mb-8 text-[var(--earth-primary)]">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Gestão Multi-Unidades</h3>
                    <p class="text-stone-500 leading-relaxed">Gerencie diferentes estúdios ou unidades no mesmo painel com total isolamento de dados.</p>
                </div>

                <div class="feature-card p-10 rounded-[var(--radius-pro)] reveal">
                    <div class="w-14 h-14 bg-[#844D3610] rounded-2xl flex items-center justify-center mb-8 text-[var(--earth-primary)]">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Lixeira Blindada</h3>
                    <p class="text-stone-500 leading-relaxed">Segurança máxima: dados apagados podem ser recuperados e não quebram seu histórico financeiro.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="precos" class="py-32 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-24 reveal">
                <h2 class="text-4xl md:text-6xl font-bold mb-4 italic">Escolha sua evolução</h2>
                <p class="text-stone-500">Planos pensados para cada fase do seu negócio.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8 items-stretch perspective-1000">

                <div class="plan-card bg-white p-12 rounded-[var(--radius-pro)] border border-stone-200 flex flex-col reveal h-full">
                    <h3 class="text-lg font-bold mb-2 text-stone-800">Iniciante</h3>
                    <div class="text-5xl font-bold mb-8 text-[var(--earth-dark)]">R$29<span class="text-sm font-normal text-stone-400">/mês</span></div>

                    <ul class="space-y-6 mb-12 flex-grow">
                        <li class="flex items-center gap-3 text-stone-600 font-medium">
                            <svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" />
                            </svg>
                            <span>1 Profissional</span>
                        </li>
                        <li class="flex items-center gap-3 text-stone-600">
                            <svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" />
                            </svg>
                            <span>Agenda Digital</span>
                        </li>
                        <li class="flex items-center gap-3 text-stone-600">
                            <svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" />
                            </svg>
                            <span>Suporte Básico</span>
                        </li>
                    </ul>
                    <a href="{{ route('register') }}" class="plan-btn block text-center py-4 rounded-xl border border-stone-300 font-bold hover:bg-stone-50 text-stone-600 transition-all">Escolher Básico</a>
                </div>

                <div class="plan-card bg-[var(--earth-dark)] p-12 rounded-[var(--radius-pro)] text-white shadow-2xl relative flex flex-col reveal border border-[var(--earth-accent)] scale-105 z-10 h-full">
                    <div class="absolute top-0 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-[var(--earth-accent)] text-[10px] font-extrabold px-6 py-1.5 rounded-full uppercase tracking-[0.2em] shadow-lg text-[var(--earth-dark)]">Mais Procurado</div>
                    <h3 class="text-xl font-bold mb-2 text-stone-100">Professional</h3>
                    <div class="text-6xl font-bold mb-8 text-white">R$79<span class="text-sm font-normal text-stone-300">/mês</span></div>

                    <ul class="space-y-6 mb-12 flex-grow">
                        <li class="flex items-center gap-3 text-stone-200">
                            <svg class="w-5 h-5 text-[var(--earth-accent)] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" />
                            </svg>
                            <span>Tudo do plano Básico</span>
                        </li>
                        <li class="flex items-center gap-3 text-stone-100 font-semibold">
                            <svg class="w-5 h-5 text-[var(--earth-accent)] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" />
                            </svg>
                            <span>Até 5 Profissionais</span>
                        </li>
                        <li class="flex items-center gap-3 text-stone-200">
                            <svg class="w-5 h-5 text-[var(--earth-accent)] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" />
                            </svg>
                            <span>WhatsApp Automático</span>
                        </li>
                        <li class="flex items-center gap-3 text-stone-200">
                            <svg class="w-5 h-5 text-[var(--earth-accent)] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" />
                            </svg>
                            <span>Ficha Anamnese Digital</span>
                        </li>
                        <li class="flex items-center gap-3 text-stone-200">
                            <svg class="w-5 h-5 text-[var(--earth-accent)] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" />
                            </svg>
                            <span>Suporte Prioritário</span>
                        </li>
                    </ul>
                    <a href="{{ route('register') }}" class="plan-btn block text-center py-5 rounded-2xl bg-[var(--earth-accent)] font-extrabold text-xl glow-effect text-[var(--earth-dark)] shadow-2xl transition-all">Assinar Agora</a>
                </div>

                <div class="plan-card bg-white p-12 rounded-[var(--radius-pro)] border border-stone-200 flex flex-col reveal h-full">
                    <h3 class="text-lg font-bold mb-2 text-stone-800">Elite Business</h3>
                    <div class="text-5xl font-bold mb-8 text-[var(--earth-dark)]">R$149<span class="text-sm font-normal text-stone-400">/mês</span></div>

                    <ul class="space-y-6 mb-12 flex-grow">
                        <li class="flex items-center gap-3 text-stone-600">
                            <svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" />
                            </svg>
                            <span>Tudo do plano Professional</span>
                        </li>
                        <li class="flex items-center gap-3 text-stone-600 font-medium">
                            <svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" />
                            </svg>
                            <span>Profissionais Ilimitados</span>
                        </li>
                        <li class="flex items-center gap-3 text-[var(--earth-primary)] font-bold">
                            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                            <span>Consultor de Marketing IA</span>
                        </li>
                        <li class="flex items-center gap-3 text-stone-600">
                            <svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" />
                            </svg>
                            <span>Gestão de Múltiplas Sedes</span>
                        </li>
                    </ul>
                    <a href="{{ route('register') }}" class="plan-btn block text-center py-4 rounded-xl border-2 border-[var(--earth-primary)] text-[var(--earth-primary)] font-bold hover:bg-[var(--earth-primary)] hover:text-white transition-all">Escolher Business</a>
                </div>
            </div>
        </div>
    </section>

    <footer class="py-20 bg-stone-50 border-t border-stone-200 text-center">
        <div class="mb-8 flex justify-center items-center gap-2 grayscale opacity-50">
            <span class="text-xs font-bold uppercase tracking-widest">Powered by Asaas & sk_test</span>
        </div>
        <p class="text-stone-400 font-medium tracking-tight">© 2026 AgendaLash. Sua arte merece uma gestão de elite.</p>
    </footer>

    <script>
        gsap.registerPlugin(ScrollTrigger);

        gsap.from(".hero-text", {
            opacity: 0,
            x: -80,
            duration: 1.2,
            ease: "power4.out"
        });
        gsap.from(".hero-card", {
            opacity: 0,
            x: 80,
            duration: 1.2,
            delay: 0.3,
            ease: "power4.out"
        });
        gsap.from(".reveal-top", {
            opacity: 0,
            y: -30,
            duration: 1,
            stagger: 0.15,
            ease: "power2.out"
        });

        gsap.utils.toArray(".reveal").forEach(elem => {
            gsap.from(elem, {
                scrollTrigger: {
                    trigger: elem,
                    start: "top 90%"
                },
                opacity: 0,
                y: 40,
                duration: 1,
                ease: "power2.out"
            });
        });

        // Tilt 3D Effect para os Planos
        const planCards = document.querySelectorAll('.plan-card');
        planCards.forEach(card => {
            card.addEventListener('mousemove', e => {
                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                const centerX = rect.width / 2;
                const centerY = rect.height / 2;

                const rotateX = ((y - centerY) / centerY) * -8; // Intensidade vertical
                const rotateY = ((x - centerX) / centerX) * 8; // Intensidade horizontal

                gsap.to(card, {
                    rotateX: rotateX,
                    rotateY: rotateY,
                    transformPerspective: 1000,
                    ease: "power2.out",
                    duration: 0.4
                });
            });

            card.addEventListener('mouseleave', () => {
                gsap.to(card, {
                    rotateX: 0,
                    rotateY: 0,
                    ease: "power3.out",
                    duration: 0.8
                });
            });
        });
        
    </script>
    
</body>

</html>