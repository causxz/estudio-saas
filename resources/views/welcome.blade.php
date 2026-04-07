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
            --earth-primary: #844D36; /* Terracota */
            --earth-accent: #C28E64;  /* Dourado Soft / Bronze */
            --earth-muted: #A8A29E;
            --radius-pro: 20px;
        }

        body { 
            font-family: 'Inter', sans-serif; 
            background-color: var(--bg-cream); 
            color: var(--earth-dark);
            overflow-x: hidden;
        }

        h1, h2, .font-serif { font-family: 'Playfair Display', serif; }

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
            border: 1px solid rgba(69, 43, 31, 0.05);
            transition: all 0.4s ease;
        }
        .feature-card:hover {
            border-color: var(--earth-accent);
            transform: translateY(-10px);
        }

        /* Animação de Brilho */
        .glow-effect {
            position: relative;
            overflow: hidden;
        }
        .glow-effect::after {
            content: '';
            position: absolute;
            top: -50%; left: -50%;
            width: 200%; height: 200%;
            background: linear-gradient(45deg, transparent, rgba(194, 142, 100, 0.1), transparent);
            transform: rotate(45deg);
            transition: 0.8s;
        }
        .glow-effect:hover::after {
            left: 100%;
        }
    </style>
</head>
<body class="antialiased">

    <nav class="glass-nav sticky top-0 z-50 py-4 px-6">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-2 reveal-top">
                <div class="w-10 h-10 bg-[var(--earth-primary)] rounded-full flex items-center justify-center text-white font-bold italic">AL</div>
                <span class="text-2xl font-bold tracking-tighter">Agenda<span class="text-[var(--earth-primary)]">Lash</span></span>
            </div>
            <div class="hidden md:flex items-center gap-10 text-sm font-semibold reveal-top">
                <a href="#features" class="hover:text-[var(--earth-primary)] transition">Serviços</a>
                <a href="#precos" class="hover:text-[var(--earth-primary)] transition">Planos</a>
                @auth
                    <a href="{{ url('/admin') }}" class="btn-earth text-white px-6 py-2.5 rounded-full">Meu Painel</a>
                @else
                    <a href="{{ route('login') }}" class="hover:text-[var(--earth-primary)]">Acessar</a>
                    <a href="{{ route('register') }}" class="btn-earth text-white px-6 py-2.5 rounded-full font-bold shadow-lg">Começar Agora</a>
                @endauth
            </div>
        </div>
    </nav>

    <header class="relative pt-16 pb-32">
        <div class="max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-16 items-center">
            
            <div class="hero-text">
                <div class="inline-block py-1 px-4 rounded-full bg-[#844D3615] text-[var(--earth-primary)] text-xs font-bold uppercase tracking-widest mb-6">
                    A evolução da Lash Designer
                </div>
                <h1 class="text-6xl md:text-7xl font-bold leading-[1.1] mb-8">
                    Organize seu estúdio com <span class="italic text-[var(--earth-primary)]">estilo e precisão.</span>
                </h1>
                <p class="text-xl text-stone-500 mb-10 leading-relaxed max-w-xl">
                    Chega de agendas de papel. Automatize seus lucros, elimine faltas e foque no olhar das suas clientes.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('register') }}" class="btn-earth text-white px-10 py-5 rounded-full font-bold text-lg glow-effect">
                        Testar Grátis por 7 Dias
                    </a>
                    <a href="#demo" class="px-8 py-5 border border-stone-200 rounded-full font-bold hover:bg-stone-50 transition">
                        Ver Vídeo Demo
                    </a>
                </div>
                <p class="text-sm text-stone-400 mt-6 flex items-center gap-2">
                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    Nenhum cartão de crédito necessário
                </p>
            </div>

            <div class="hero-card lg:block">
                <div class="bg-white p-10 rounded-[var(--radius-pro)] shadow-[0_30px_100px_-20px_rgba(69,43,31,0.15)] border border-stone-100 relative">
                    <div class="absolute -top-10 -right-10 w-32 h-32 bg-[var(--earth-accent)] opacity-10 rounded-full blur-3xl"></div>
                    <h2 class="text-2xl font-bold mb-8">Entrar no Sistema</h2>
                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf
                        <div>
                            <label class="text-xs font-bold uppercase tracking-wider text-stone-400 block mb-2 ml-1">E-mail Profissional</label>
                            <input type="email" name="email" required class="w-full p-4 bg-stone-50 border-none rounded-2xl focus:ring-2 focus:ring-[var(--earth-accent)] transition outline-none" placeholder="seu@email.com">
                        </div>
                        <div>
                            <label class="text-xs font-bold uppercase tracking-wider text-stone-400 block mb-2 ml-1">Senha</label>
                            <input type="password" name="password" required class="w-full p-4 bg-stone-50 border-none rounded-2xl focus:ring-2 focus:ring-[var(--earth-accent)] transition outline-none" placeholder="••••••••">
                        </div>
                        <button class="w-full btn-earth text-white py-4 rounded-2xl font-bold text-lg shadow-xl shadow-[#844D3640]">
                            Acessar Minha Agenda
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </header>

    <section id="features" class="py-32 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-20 reveal">
                <h2 class="text-4xl md:text-5xl font-bold mb-4">Tudo sob seu controle</h2>
                <div class="w-20 h-1 bg-[var(--earth-accent)] mx-auto"></div>
            </div>
            
            <div class="grid md:grid-cols-3 gap-10">
                <div class="feature-card p-10 rounded-[var(--radius-pro)] reveal">
                    <div class="w-16 h-16 bg-[#844D3610] rounded-2xl flex items-center justify-center mb-8 text-[var(--earth-primary)]">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Agenda Inteligente</h3>
                    <p class="text-stone-500 leading-relaxed">Bloqueio de conflitos em tempo real. Cada profissional tem sua visão clara e organizada.</p>
                </div>
                
                <div class="feature-card p-10 rounded-[var(--radius-pro)] reveal">
                    <div class="w-16 h-16 bg-[#844D3610] rounded-2xl flex items-center justify-center mb-8 text-[var(--earth-primary)]">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Comissões Automáticas</h3>
                    <p class="text-stone-500 leading-relaxed">Saiba exatamente quanto cada designer deve receber. O sistema faz o cálculo por você.</p>
                </div>

                <div class="feature-card p-10 rounded-[var(--radius-pro)] reveal">
                    <div class="w-16 h-16 bg-[#844D3610] rounded-2xl flex items-center justify-center mb-8 text-[var(--earth-primary)]">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Lembretes de Elite</h3>
                    <p class="text-stone-500 leading-relaxed">Reduza faltas enviando confirmações automáticas de agendamento via WhatsApp.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="precos" class="py-32 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-20 reveal">
                <h2 class="text-4xl md:text-5xl font-bold mb-4 italic">Escolha sua evolução</h2>
                <p class="text-stone-500">Transparência total para o seu estúdio crescer.</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8 items-end">
                <div class="bg-white p-10 rounded-[var(--radius-pro)] border border-stone-100 reveal">
                    <h3 class="text-lg font-bold mb-2">Individual</h3>
                    <div class="text-4xl font-bold mb-6 text-[var(--earth-dark)]">R$39<span class="text-sm font-normal text-stone-400">/mês</span></div>
                    <ul class="space-y-4 mb-10 text-stone-600">
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-[var(--earth-accent)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"></path></svg> 1 Profissional</li>
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-[var(--earth-accent)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"></path></svg> Agenda Digital</li>
                    </ul>
                    <a href="{{ route('register') }}" class="block text-center py-4 rounded-xl border border-stone-200 font-bold hover:bg-stone-50 transition">Começar</a>
                </div>

                <div class="bg-[var(--earth-dark)] p-12 rounded-[var(--radius-pro)] text-white scale-105 shadow-2xl relative reveal">
                    <div class="absolute top-0 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-[var(--earth-accent)] text-xs font-bold px-4 py-1 rounded-full uppercase tracking-tighter">Mais Vendido</div>
                    <h3 class="text-xl font-bold mb-2">Professional</h3>
                    <div class="text-5xl font-bold mb-6">R$79<span class="text-sm font-normal text-stone-300">/mês</span></div>
                    <ul class="space-y-4 mb-10 text-stone-300">
                        <li class="flex items-center gap-2"><svg class="w-5 h-5 text-[var(--earth-accent)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" d="M5 13l4 4L19 7"></path></svg> Até 5 Profissionais</li>
                        <li class="flex items-center gap-2"><svg class="w-5 h-5 text-[var(--earth-accent)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" d="M5 13l4 4L19 7"></path></svg> WhatsApp Automático</li>
                        <li class="flex items-center gap-2"><svg class="w-5 h-5 text-[var(--earth-accent)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" d="M5 13l4 4L19 7"></path></svg> Relatórios de Comissão</li>
                    </ul>
                    <a href="{{ route('register') }}" class="block text-center py-4 rounded-xl bg-[var(--earth-accent)] font-bold text-lg glow-effect">Testar Agora</a>
                </div>

                <div class="bg-white p-10 rounded-[var(--radius-pro)] border border-stone-100 reveal">
                    <h3 class="text-lg font-bold mb-2">Business</h3>
                    <div class="text-4xl font-bold mb-6 text-[var(--earth-dark)]">R$149<span class="text-sm font-normal text-stone-400">/mês</span></div>
                    <ul class="space-y-4 mb-10 text-stone-600">
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-[var(--earth-accent)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"></path></svg> Designers Ilimitadas</li>
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-[var(--earth-accent)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"></path></svg> Suporte Prioritário</li>
                    </ul>
                    <a href="{{ route('register') }}" class="block text-center py-4 rounded-xl border border-stone-200 font-bold hover:bg-stone-50 transition">Falar Conosco</a>
                </div>
            </div>
        </div>
    </section>

    <footer class="py-20 bg-stone-50 border-t border-stone-200 text-center">
        <p class="text-stone-400 font-medium">© 2026 AgendaLash. Sua arte merece uma gestão de elite.</p>
    </footer>

    <script>
        // GSAP ANIMATIONS
        gsap.registerPlugin(ScrollTrigger);

        // Entrada do Hero
        gsap.from(".hero-text", { opacity: 0, x: -100, duration: 1.2, ease: "power4.out" });
        gsap.from(".hero-card", { opacity: 0, x: 100, duration: 1.2, delay: 0.3, ease: "power4.out" });
        gsap.from(".reveal-top", { opacity: 0, y: -50, duration: 1, stagger: 0.2, ease: "power2.out" });

        // Revelação ao Scroll
        gsap.utils.toArray(".reveal").forEach(elem => {
            gsap.from(elem, {
                scrollTrigger: elem,
                opacity: 0,
                y: 50,
                duration: 1,
                ease: "power2.out"
            });
        });
    </script>

</body>
</html>