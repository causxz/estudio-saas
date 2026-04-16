<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AgendaLash | Gestão de Elite para Estúdios</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

    <style>
        :root {
            --cream: #FDFBF7;
            --earth-dark: #3A2318;
            --earth-mid: #6B3728;
            --terra: #844D36;
            --bronze: #C28E64;
            --bronze-light: #E8C9A8;
            --bronze-pale: #F7EDE0;
            --r: 20px;
            --r-lg: 32px;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--cream);
            color: var(--earth-dark);
            overflow-x: hidden;
            cursor: none;
        }

        /* ── CURSOR ──────────────────────────────── */
        .cursor-dot {
            width: 8px; height: 8px;
            background: var(--terra);
            border-radius: 50%;
            position: fixed;
            pointer-events: none;
            z-index: 9999;
            transition: transform 0.1s ease, opacity 0.2s;
            transform: translate(-50%, -50%);
        }
        .cursor-ring {
            width: 32px; height: 32px;
            border: 1.5px solid var(--terra);
            border-radius: 50%;
            position: fixed;
            pointer-events: none;
            z-index: 9999;
            transition: width 0.3s, height 0.3s, background 0.3s, border-color 0.3s;
            transform: translate(-50%, -50%);
            opacity: 0.6;
        }
        body.hovering .cursor-ring {
            width: 48px; height: 48px;
            background: rgba(132, 77, 54, 0.08);
            border-color: var(--bronze);
        }

        /* ── TYPOGRAPHY ──────────────────────────── */
        h1, h2, h3, .serif { font-family: 'DM Serif Display', serif; }

        /* ── NAV ─────────────────────────────────── */
        nav {
            position: fixed; top: 0; left: 0; right: 0; z-index: 100;
            padding: 0 40px;
            height: 68px;
            display: flex; align-items: center; justify-content: space-between;
            background: rgba(253, 251, 247, 0.85);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(58, 35, 24, 0.06);
            transition: background 0.4s;
        }
        .nav-logo {
            display: flex; align-items: center; gap: 10px;
            text-decoration: none; color: var(--earth-dark);
        }
        .nav-logo-mark {
            width: 36px; height: 36px;
            background: var(--terra);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            color: white; font-family: 'DM Serif Display', serif;
            font-size: 14px; font-style: italic;
        }
        .nav-logo-text { font-size: 18px; font-weight: 600; letter-spacing: -0.02em; }
        .nav-logo-text span { color: var(--terra); }
        .nav-links { display: flex; align-items: center; gap: 32px; }
        .nav-links a {
            font-size: 14px; font-weight: 500;
            color: var(--earth-dark); text-decoration: none;
            opacity: 0.65; transition: opacity 0.25s;
        }
        .nav-links a:hover { opacity: 1; }
        .nav-cta {
            background: var(--terra); color: white !important; opacity: 1 !important;
            padding: 8px 22px; border-radius: 100px;
            font-size: 13px !important; font-weight: 600 !important;
            transition: background 0.25s, transform 0.25s !important;
        }
        .nav-cta:hover { background: var(--earth-dark) !important; transform: scale(1.04); }

        /* ── HERO ────────────────────────────────── */
        .hero {
            min-height: 100vh;
            padding-top: 68px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
            overflow: hidden;
        }
        .hero-left {
            padding: 80px 60px 80px 80px;
            display: flex; flex-direction: column; justify-content: center;
            position: relative;
        }
        .hero-badge {
            display: inline-flex; align-items: center; gap: 6px;
            background: var(--bronze-pale);
            color: var(--terra);
            font-size: 11px; font-weight: 600;
            letter-spacing: 0.12em; text-transform: uppercase;
            padding: 6px 14px; border-radius: 100px;
            border: 1px solid rgba(194, 142, 100, 0.3);
            width: fit-content; margin-bottom: 28px;
        }
        .hero-badge::before {
            content: '';
            width: 6px; height: 6px; border-radius: 50%;
            background: var(--terra); animation: pulse-dot 2s infinite;
        }
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.7); }
        }
        .hero-h1 {
            font-size: clamp(44px, 5vw, 68px);
            line-height: 1.05;
            letter-spacing: -0.02em;
            margin-bottom: 24px;
            color: var(--earth-dark);
        }
        .hero-h1 em { color: var(--terra); font-style: italic; }
        .hero-sub {
            font-size: 17px; line-height: 1.7; color: #7A6355;
            max-width: 480px; margin-bottom: 44px;
        }
        .hero-actions { display: flex; align-items: center; gap: 16px; flex-wrap: wrap; }
        .btn-primary {
            background: var(--earth-dark); color: white;
            padding: 16px 36px; border-radius: 100px;
            font-size: 15px; font-weight: 600;
            text-decoration: none;
            display: inline-flex; align-items: center; gap: 8px;
            transition: background 0.3s, transform 0.3s, box-shadow 0.3s;
            position: relative; overflow: hidden;
        }
        .btn-primary::after {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.08), transparent);
            opacity: 0; transition: opacity 0.3s;
        }
        .btn-primary:hover { background: var(--terra); transform: translateY(-2px); box-shadow: 0 16px 40px rgba(132,77,54,0.25); }
        .btn-primary:hover::after { opacity: 1; }
        .btn-ghost {
            color: var(--earth-dark); font-size: 14px; font-weight: 500;
            text-decoration: none;
            display: inline-flex; align-items: center; gap: 6px;
            opacity: 0.6; transition: opacity 0.25s;
        }
        .btn-ghost:hover { opacity: 1; }
        .btn-ghost svg { transition: transform 0.25s; }
        .btn-ghost:hover svg { transform: translateX(3px); }
        .hero-trust {
            display: flex; align-items: center; gap: 10px;
            margin-top: 32px; font-size: 12px; color: #8A7060; font-style: italic;
        }
        .hero-trust svg { color: #62A86E; flex-shrink: 0; }

        /* HERO RIGHT — decorative panel */
        .hero-right {
            background: var(--earth-dark);
            position: relative; overflow: hidden;
            display: flex; align-items: center; justify-content: center;
            padding: 60px 50px;
        }
        .hero-right::before {
            content: '';
            position: absolute; top: -100px; right: -100px;
            width: 400px; height: 400px; border-radius: 50%;
            background: radial-gradient(circle, rgba(194,142,100,0.15) 0%, transparent 70%);
        }
        .hero-right::after {
            content: '';
            position: absolute; bottom: -80px; left: -80px;
            width: 300px; height: 300px; border-radius: 50%;
            background: radial-gradient(circle, rgba(132,77,54,0.2) 0%, transparent 70%);
        }

        /* Login card */
        .login-card {
            background: rgba(253, 251, 247, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(194, 142, 100, 0.15);
            border-radius: var(--r-lg);
            padding: 40px;
            width: 100%; max-width: 400px;
            position: relative; z-index: 1;
        }
        .login-card h2 {
            font-size: 22px; font-weight: 600;
            color: rgba(253,251,247,0.95);
            margin-bottom: 28px;
        }
        .field-label {
            font-size: 11px; font-weight: 600;
            letter-spacing: 0.1em; text-transform: uppercase;
            color: rgba(253,251,247,0.45);
            display: block; margin-bottom: 8px; margin-left: 2px;
        }
        .field-input {
            width: 100%; padding: 14px 18px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(194,142,100,0.15);
            border-radius: 14px; color: rgba(253,251,247,0.9);
            font-size: 14px; font-family: 'DM Sans', sans-serif;
            outline: none; margin-bottom: 18px;
            transition: border-color 0.25s, background 0.25s;
        }
        .field-input::placeholder { color: rgba(253,251,247,0.2); }
        .field-input:focus {
            border-color: rgba(194,142,100,0.5);
            background: rgba(255,255,255,0.09);
        }
        .btn-login {
            width: 100%; padding: 15px;
            background: var(--terra); color: white;
            border: none; border-radius: 14px;
            font-size: 15px; font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            transition: background 0.25s, transform 0.2s;
        }
        .btn-login:hover { background: var(--bronze); transform: translateY(-1px); }
        .error-box {
            background: rgba(220,50,50,0.12); border: 1px solid rgba(220,50,50,0.2);
            color: #ffaaaa; font-size: 13px; padding: 12px 16px;
            border-radius: 10px; margin-bottom: 16px;
        }
        .register-link {
            text-align: center; margin-top: 20px;
            font-size: 13px; color: rgba(253,251,247,0.4);
        }
        .register-link a { color: var(--bronze); text-decoration: none; font-weight: 500; }
        .register-link a:hover { color: var(--bronze-light); }

        /* ── STATS STRIP ─────────────────────────── */
        .stats-strip {
            display: grid; grid-template-columns: repeat(4, 1fr);
            background: var(--earth-dark);
            padding: 40px 80px;
        }
        .stat-item {
            display: flex; flex-direction: column; align-items: center;
            padding: 16px; gap: 4px;
            border-right: 1px solid rgba(194,142,100,0.12);
        }
        .stat-item:last-child { border-right: none; }
        .stat-number {
            font-family: 'DM Serif Display', serif;
            font-size: 40px; color: var(--bronze);
            line-height: 1;
        }
        .stat-label {
            font-size: 12px; color: rgba(253,251,247,0.4);
            text-transform: uppercase; letter-spacing: 0.08em; font-weight: 500;
        }

        /* ── FEATURES ────────────────────────────── */
        .features { padding: 120px 80px; }
        .section-eyebrow {
            font-size: 11px; font-weight: 600; letter-spacing: 0.15em;
            text-transform: uppercase; color: var(--terra);
            margin-bottom: 14px;
        }
        .section-title {
            font-size: clamp(32px, 4vw, 52px);
            line-height: 1.1; letter-spacing: -0.02em;
            margin-bottom: 16px;
        }
        .section-sub { font-size: 16px; color: #7A6355; max-width: 520px; line-height: 1.65; }

        .features-grid {
            display: grid; grid-template-columns: repeat(3, 1fr);
            gap: 2px; background: rgba(58,35,24,0.06);
            border-radius: var(--r-lg); overflow: hidden;
            margin-top: 64px;
        }
        .feat-cell {
            background: var(--cream);
            padding: 36px 32px;
            transition: background 0.3s;
            cursor: default;
            position: relative; overflow: hidden;
        }
        .feat-cell::before {
            content: '';
            position: absolute; inset: 0;
            background: var(--bronze-pale);
            opacity: 0; transition: opacity 0.35s;
        }
        .feat-cell:hover::before { opacity: 1; }
        .feat-cell > * { position: relative; z-index: 1; }
        .feat-icon {
            width: 44px; height: 44px;
            background: var(--bronze-pale); border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            color: var(--terra); margin-bottom: 20px;
            transition: background 0.3s, transform 0.35s;
        }
        .feat-cell:hover .feat-icon {
            background: var(--terra); color: white;
            transform: rotate(-6deg) scale(1.1);
        }
        .feat-title { font-size: 16px; font-weight: 600; margin-bottom: 10px; }
        .feat-desc { font-size: 14px; color: #8A7060; line-height: 1.6; }

        /* plan tag inside feature */
        .plan-tag {
            display: inline-block; margin-top: 14px;
            font-size: 10px; font-weight: 700;
            letter-spacing: 0.1em; text-transform: uppercase;
            padding: 4px 10px; border-radius: 100px;
        }
        .plan-tag.free { background: var(--bronze-pale); color: var(--terra); }
        .plan-tag.plus {
            background: var(--earth-dark); color: var(--bronze-light);
        }

        /* ── PRICING ─────────────────────────────── */
        .pricing {
            padding: 100px 80px 140px;
            background: var(--earth-dark);
            position: relative; overflow: hidden;
        }
        .pricing::before {
            content: '';
            position: absolute; top: 0; left: 50%; transform: translateX(-50%);
            width: 80%; height: 1px;
            background: linear-gradient(to right, transparent, rgba(194,142,100,0.25), transparent);
        }
        .pricing .section-eyebrow { color: var(--bronze); }
        .pricing .section-title { color: rgba(253,251,247,0.95); }
        .pricing .section-sub { color: rgba(253,251,247,0.45); }

        .plans-grid {
            display: grid; grid-template-columns: 1fr 1fr;
            gap: 24px; margin-top: 64px; max-width: 860px;
        }
        .plan-card {
            border-radius: var(--r-lg);
            padding: 44px;
            position: relative; overflow: hidden;
            transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .plan-card:hover { transform: translateY(-6px); }

        /* FREE plan */
        .plan-free {
            background: rgba(253,251,247,0.04);
            border: 1px solid rgba(194,142,100,0.12);
        }
        .plan-free .plan-name { color: rgba(253,251,247,0.6); }
        .plan-free .plan-price { color: rgba(253,251,247,0.95); }
        .plan-free .plan-desc { color: rgba(253,251,247,0.35); }
        .plan-free .feat-li { color: rgba(253,251,247,0.65); }
        .plan-free .plan-btn-wrap .plan-btn {
            border: 1px solid rgba(194,142,100,0.25);
            color: rgba(253,251,247,0.7);
            background: transparent;
        }
        .plan-free .plan-btn-wrap .plan-btn:hover {
            border-color: rgba(194,142,100,0.5);
            color: rgba(253,251,247,0.95);
            background: rgba(194,142,100,0.08);
        }

        /* PLUS plan */
        .plan-plus {
            background: var(--terra);
            border: 1px solid rgba(255,255,255,0.08);
        }
        .plan-plus::before {
            content: '';
            position: absolute; top: -60px; right: -60px;
            width: 200px; height: 200px; border-radius: 50%;
            background: radial-gradient(circle, rgba(255,255,255,0.07) 0%, transparent 70%);
        }
        .plan-popular {
            position: absolute; top: 24px; right: 24px;
            background: var(--bronze); color: var(--earth-dark);
            font-size: 10px; font-weight: 700; letter-spacing: 0.12em;
            text-transform: uppercase; padding: 5px 14px; border-radius: 100px;
        }
        .plan-plus .plan-name { color: rgba(255,255,255,0.7); }
        .plan-plus .plan-price { color: white; }
        .plan-plus .plan-desc { color: rgba(255,255,255,0.5); }
        .plan-plus .feat-li { color: rgba(255,255,255,0.85); }
        .plan-plus .plan-btn-wrap .plan-btn {
            background: white; color: var(--terra); border: none;
        }
        .plan-plus .plan-btn-wrap .plan-btn:hover {
            background: var(--earth-dark); color: white;
        }

        .plan-name { font-size: 13px; font-weight: 600; letter-spacing: 0.04em; margin-bottom: 12px; }
        .plan-price-row { display: flex; align-items: baseline; gap: 6px; margin-bottom: 8px; }
        .plan-price { font-family: 'DM Serif Display', serif; font-size: 52px; line-height: 1; }
        .plan-price-period { font-size: 14px; opacity: 0.5; }
        .plan-price-free-label { font-family: 'DM Serif Display', serif; font-size: 44px; line-height: 1; }
        .plan-desc { font-size: 13px; line-height: 1.5; margin-bottom: 28px; }
        .feat-list { list-style: none; margin-bottom: 36px; display: flex; flex-direction: column; gap: 12px; }
        .feat-li {
            display: flex; align-items: flex-start; gap: 10px;
            font-size: 14px; line-height: 1.4;
        }
        .feat-li-icon {
            width: 18px; height: 18px; border-radius: 50%;
            flex-shrink: 0; margin-top: 1px;
            display: flex; align-items: center; justify-content: center;
        }
        .plan-free .feat-li-icon { background: rgba(194,142,100,0.15); color: var(--bronze); }
        .plan-plus .feat-li-icon { background: rgba(255,255,255,0.15); color: white; }
        .plan-btn {
            display: block; width: 100%; text-align: center;
            padding: 15px; border-radius: 14px;
            font-size: 14px; font-weight: 600;
            text-decoration: none; transition: background 0.25s, color 0.25s, transform 0.2s;
            cursor: pointer;
        }
        .plan-btn:hover { transform: translateY(-2px); }

        /* ── COMPARISON TOGGLE ───────────────────── */
        .compare-section {
            padding: 80px;
            background: var(--earth-dark);
            border-top: 1px solid rgba(194,142,100,0.08);
        }
        .compare-toggle {
            display: flex; align-items: center; gap: 0;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(194,142,100,0.12);
            border-radius: 100px; padding: 4px; width: fit-content;
            margin: 0 auto 48px; cursor: pointer;
        }
        .toggle-opt {
            padding: 10px 28px; border-radius: 100px;
            font-size: 13px; font-weight: 600;
            color: rgba(253,251,247,0.4);
            transition: all 0.3s; user-select: none;
        }
        .toggle-opt.active {
            background: var(--terra); color: white;
        }
        .compare-table {
            width: 100%; border-collapse: collapse; max-width: 640px; margin: 0 auto;
        }
        .compare-table th {
            font-size: 12px; font-weight: 600;
            letter-spacing: 0.08em; text-transform: uppercase;
            color: rgba(253,251,247,0.3);
            padding: 0 0 20px; text-align: left;
        }
        .compare-table th:not(:first-child) { text-align: center; }
        .compare-table td {
            padding: 16px 0;
            border-top: 1px solid rgba(194,142,100,0.06);
            font-size: 14px;
        }
        .compare-table td:first-child { color: rgba(253,251,247,0.6); }
        .compare-table td:not(:first-child) { text-align: center; }
        .check-yes { color: var(--bronze); font-size: 16px; }
        .check-no { color: rgba(253,251,247,0.15); font-size: 16px; }
        .compare-table .col-plus td:not(:first-child):last-child { color: var(--terra); font-weight: 600; }

        /* ── FOOTER ──────────────────────────────── */
        footer {
            background: #1E0F09; padding: 48px 80px;
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 20px;
        }
        footer .logo-text { font-size: 16px; font-weight: 600; color: rgba(253,251,247,0.4); }
        footer .logo-text span { color: var(--terra); }
        footer p { font-size: 13px; color: rgba(253,251,247,0.2); }

        /* ── SCROLLING MARQUEE ───────────────────── */
        .marquee-wrap {
            overflow: hidden; background: var(--bronze-pale);
            padding: 16px 0; border-top: 1px solid rgba(194,142,100,0.15);
            border-bottom: 1px solid rgba(194,142,100,0.15);
        }
        .marquee-track {
            display: flex; gap: 48px;
            animation: marquee 28s linear infinite;
            white-space: nowrap; width: max-content;
        }
        .marquee-track span {
            font-size: 12px; font-weight: 600; letter-spacing: 0.1em;
            text-transform: uppercase; color: var(--terra); opacity: 0.6;
            display: flex; align-items: center; gap: 10px;
        }
        .marquee-track span::after {
            content: '◆'; font-size: 8px;
        }
        @keyframes marquee { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }

        /* ── RESPONSIVE ──────────────────────────── */
        @media (max-width: 1024px) {
            .hero { grid-template-columns: 1fr; min-height: auto; }
            .hero-left { padding: 60px 40px 40px; }
            .hero-right { min-height: 460px; }
            .stats-strip { grid-template-columns: repeat(2,1fr); padding: 40px; }
            .features { padding: 80px 40px; }
            .features-grid { grid-template-columns: repeat(2,1fr); }
            .pricing { padding: 80px 40px 100px; }
            .plans-grid { max-width: 100%; }
            .compare-section { padding: 60px 40px; }
            footer { padding: 40px; }
        }
        @media (max-width: 640px) {
            .hero-left { padding: 50px 24px 32px; }
            .nav { padding: 0 20px; }
            .features-grid { grid-template-columns: 1fr; }
            .plans-grid { grid-template-columns: 1fr; }
            .stats-strip { grid-template-columns: repeat(2,1fr); padding: 24px; }
            footer { flex-direction: column; align-items: flex-start; padding: 32px 24px; }
        }

        /* ── ANIMATIONS ──────────────────────────── */
        .fade-up { opacity: 0; transform: translateY(32px); }
        .fade-left { opacity: 0; transform: translateX(-32px); }
    </style>
</head>

<body class="antialiased">

    <!-- Custom Cursor -->
    <div class="cursor-dot" id="cursorDot"></div>
    <div class="cursor-ring" id="cursorRing"></div>

    <!-- NAV -->
    <nav id="mainNav">
        <a href="/" class="nav-logo">
            <div class="nav-logo-mark">AL</div>
            <span class="nav-logo-text">Agenda<span>Lash</span></span>
        </a>
        <div class="nav-links">
            <a href="#features">Funcionalidades</a>
            <a href="#precos">Planos</a>
            @auth
                <a href="{{ url('/admin') }}" class="nav-cta">Meu Painel</a>
            @else
                <a href="#login-form" class="nav-cta">Acessar</a>
            @endauth
        </div>
    </nav>

    <!-- HERO -->
    <header class="hero" id="home">
        <div class="hero-left">
            <div class="hero-badge fade-left">Plataforma para Lash & Beauty Studios</div>

            <h1 class="hero-h1 fade-up" style="animation-delay:0.1s">
                Sua agenda,<br>
                seu <em>estúdio</em>,<br>
                seu controle.
            </h1>

            <p class="hero-sub fade-up" style="animation-delay:0.2s">
                A gestão que transforma cabeleireiras e lashistas em empresárias. Agenda, finanças, anamnese e automações — tudo num só lugar.
            </p>

            <div class="hero-actions fade-up" style="animation-delay:0.3s">
                <a href="{{ route('register') }}" class="btn-primary">
                    Começar grátis
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
                <a href="#precos" class="btn-ghost">
                    Ver planos
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
            </div>

            <p class="hero-trust fade-up" style="animation-delay:0.4s">
                <svg width="14" height="14" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                </svg>
                Sem cartão de crédito &nbsp;·&nbsp; Plano gratuito para sempre disponível
            </p>
        </div>

        <div class="hero-right">
            <div class="login-card fade-left" id="login-form" style="animation-delay:0.2s">
                <h2>Acessar sistema</h2>

                <form method="POST" action="{{ route('processar.login') }}">
                    @csrf

                    @error('email')
                        <div class="error-box">{{ $message }}</div>
                    @enderror

                    <label class="field-label">E-mail profissional</label>
                    <input type="email" name="email" class="field-input" placeholder="lash@seuestudio.com" required>

                    <label class="field-label">Senha</label>
                    <input type="password" name="password" class="field-input" placeholder="••••••••" required>

                    <button type="submit" class="btn-login">Entrar na agenda</button>
                </form>

                <p class="register-link">
                    Não tem conta? <a href="{{ route('register') }}">Criar gratuitamente</a>
                </p>
            </div>
        </div>
    </header>

    <!-- MARQUEE STRIP -->
    <div class="marquee-wrap">
        <div class="marquee-track" id="marqueeTrack">
            <span>Agenda Visual Pro</span>
            <span>Dashboard Financeiro</span>
            <span>Anamnese Digital</span>
            <span>WhatsApp Automático</span>
            <span>Comissões Automáticas</span>
            <span>Consultor IA de Marketing</span>
            <span>Múltiplos Profissionais</span>
            <span>Lixeira Blindada</span>
            <span>Suporte Prioritário</span>
            <!-- duplicate for infinite scroll -->
            <span>Agenda Visual Pro</span>
            <span>Dashboard Financeiro</span>
            <span>Anamnese Digital</span>
            <span>WhatsApp Automático</span>
            <span>Comissões Automáticas</span>
            <span>Consultor IA de Marketing</span>
            <span>Múltiplos Profissionais</span>
            <span>Lixeira Blindada</span>
            <span>Suporte Prioritário</span>
        </div>
    </div>

    <!-- STATS -->
    <div class="stats-strip">
        <div class="stat-item fade-up">
            <span class="stat-number" data-count="1200">0</span>
            <span class="stat-label">Agendamentos / mês</span>
        </div>
        <div class="stat-item fade-up">
            <span class="stat-number" data-count="80">0</span>
            <span class="stat-label">% menos faltas</span>
        </div>
        <div class="stat-item fade-up">
            <span class="stat-number" data-count="7">∞</span>
            <span class="stat-label">Dias grátis para testar</span>
        </div>
        <div class="stat-item fade-up">
            <span class="stat-number" data-count="24">0</span>
            <span class="stat-label">Horas de suporte</span>
        </div>
    </div>

    <!-- FEATURES -->
    <section id="features" class="features">
        <div class="fade-up">
            <p class="section-eyebrow">Funcionalidades</p>
            <h2 class="section-title">Tudo sob seu controle</h2>
            <p class="section-sub">A tecnologia que trabalha enquanto você transforma olhares. Cada ferramenta pensada para a realidade do seu estúdio.</p>
        </div>

        <div class="features-grid">

            <div class="feat-cell fade-up">
                <div class="feat-icon">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="feat-title">Agenda Visual Pro</h3>
                <p class="feat-desc">Visão semanal por profissional com bloqueio inteligente de conflitos de horários.</p>
                <span class="plan-tag free">Gratuito</span>
            </div>

            <div class="feat-cell fade-up">
                <div class="feat-icon">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="feat-title">Dashboard Financeiro</h3>
                <p class="feat-desc">Cálculo instantâneo de ganhos, comissões por profissional e controle de caixa.</p>
                <span class="plan-tag free">Gratuito</span>
            </div>

            <div class="feat-cell fade-up">
                <div class="feat-icon">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <h3 class="feat-title">Anamnese Digital</h3>
                <p class="feat-desc">Fichas de saúde customizadas com assinatura digital coletada pelo celular da cliente.</p>
                <span class="plan-tag free">Gratuito</span>
            </div>

            <div class="feat-cell fade-up">
                <div class="feat-icon">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/></svg>
                </div>
                <h3 class="feat-title">WhatsApp Automático</h3>
                <p class="feat-desc">Redução de até 80% de faltas com disparos automáticos de confirmação e lembrete.</p>
                <span class="plan-tag plus">Plus</span>
            </div>

            <div class="feat-cell fade-up">
                <div class="feat-icon">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                </div>
                <h3 class="feat-title">Consultor de Marketing IA</h3>
                <p class="feat-desc">Sugestões inteligentes de campanhas, datas sazonais e estratégias de fidelização.</p>
                <span class="plan-tag plus">Plus</span>
            </div>

            <div class="feat-cell fade-up">
                <div class="feat-icon">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <h3 class="feat-title">Lixeira Blindada</h3>
                <p class="feat-desc">Dados apagados podem ser recuperados sem quebrar seu histórico financeiro.</p>
                <span class="plan-tag free">Gratuito</span>
            </div>

        </div>
    </section>

    <!-- PRICING -->
    <section id="precos" class="pricing">
        <div class="text-center fade-up" style="max-width: 560px; margin: 0 auto;">
            <p class="section-eyebrow">Planos</p>
            <h2 class="section-title">Escolha sua evolução</h2>
            <p class="section-sub">Comece grátis e escale quando seu estúdio crescer.</p>
        </div>

        <div class="plans-grid fade-up" style="margin: 64px auto 0;">

            <!-- FREE -->
            <div class="plan-card plan-free" id="planFree">
                <p class="plan-name">Gratuito</p>
                <div class="plan-price-row">
                    <span class="plan-price-free-label">R$0</span>
                    <span class="plan-price-period">/mês</span>
                </div>
                <p class="plan-desc">Tudo que você precisa para começar a organizar seu estúdio.</p>

                <ul class="feat-list">
                    <li class="feat-li">
                        <div class="feat-li-icon">
                            <svg width="10" height="10" viewBox="0 0 12 12" fill="currentColor"><path d="M10.28 2.28L4 8.56 1.72 6.28.28 7.72l3.44 3.44 7.72-7.72z"/></svg>
                        </div>
                        Dashboard financeiro completo
                    </li>
                    <li class="feat-li">
                        <div class="feat-li-icon">
                            <svg width="10" height="10" viewBox="0 0 12 12" fill="currentColor"><path d="M10.28 2.28L4 8.56 1.72 6.28.28 7.72l3.44 3.44 7.72-7.72z"/></svg>
                        </div>
                        Agenda digital visual
                    </li>
                    <li class="feat-li">
                        <div class="feat-li-icon">
                            <svg width="10" height="10" viewBox="0 0 12 12" fill="currentColor"><path d="M10.28 2.28L4 8.56 1.72 6.28.28 7.72l3.44 3.44 7.72-7.72z"/></svg>
                        </div>
                        1 profissional
                    </li>
                    <li class="feat-li">
                        <div class="feat-li-icon">
                            <svg width="10" height="10" viewBox="0 0 12 12" fill="currentColor"><path d="M10.28 2.28L4 8.56 1.72 6.28.28 7.72l3.44 3.44 7.72-7.72z"/></svg>
                        </div>
                        Anamnese digital + assinatura
                    </li>
                </ul>

                <div class="plan-btn-wrap">
                    <a href="{{ route('register') }}" class="plan-btn">Começar gratuitamente</a>
                </div>
            </div>

            <!-- PLUS -->
            <div class="plan-card plan-plus" id="planPlus">
                <span class="plan-popular">Mais popular</span>
                <p class="plan-name">Plus</p>
                <div class="plan-price-row">
                    <span class="plan-price">R$79</span>
                    <span class="plan-price-period">,99/mês</span>
                </div>
                <p class="plan-desc">Para quem quer crescer com automação e inteligência.</p>

                <ul class="feat-list">
                    <li class="feat-li">
                        <div class="feat-li-icon">
                            <svg width="10" height="10" viewBox="0 0 12 12" fill="currentColor"><path d="M10.28 2.28L4 8.56 1.72 6.28.28 7.72l3.44 3.44 7.72-7.72z"/></svg>
                        </div>
                        Tudo do plano Gratuito
                    </li>
                    <li class="feat-li">
                        <div class="feat-li-icon">
                            <svg width="10" height="10" viewBox="0 0 12 12" fill="currentColor"><path d="M10.28 2.28L4 8.56 1.72 6.28.28 7.72l3.44 3.44 7.72-7.72z"/></svg>
                        </div>
                        Até 5 profissionais
                    </li>
                    <li class="feat-li">
                        <div class="feat-li-icon">
                            <svg width="10" height="10" viewBox="0 0 12 12" fill="currentColor"><path d="M10.28 2.28L4 8.56 1.72 6.28.28 7.72l3.44 3.44 7.72-7.72z"/></svg>
                        </div>
                        WhatsApp automático de agendamentos
                    </li>
                    <li class="feat-li">
                        <div class="feat-li-icon">
                            <svg width="10" height="10" viewBox="0 0 12 12" fill="currentColor"><path d="M10.28 2.28L4 8.56 1.72 6.28.28 7.72l3.44 3.44 7.72-7.72z"/></svg>
                        </div>
                        Consultor de Marketing IA
                    </li>
                    <li class="feat-li">
                        <div class="feat-li-icon">
                            <svg width="10" height="10" viewBox="0 0 12 12" fill="currentColor"><path d="M10.28 2.28L4 8.56 1.72 6.28.28 7.72l3.44 3.44 7.72-7.72z"/></svg>
                        </div>
                        Suporte prioritário
                    </li>
                </ul>

                <div class="plan-btn-wrap">
                    <a href="{{ route('register') }}" class="plan-btn">Assinar Plus agora</a>
                </div>
            </div>

        </div>
    </section>

    <!-- COMPARISON TABLE -->
    <section class="compare-section">
        <h2 class="serif fade-up" style="font-size:28px; color:rgba(253,251,247,0.85); text-align:center; margin-bottom:36px; letter-spacing:-0.01em;">Compare os planos</h2>

        <div class="compare-toggle fade-up" id="compareToggle">
            <div class="toggle-opt active" data-tab="all">Todas as funções</div>
            <div class="toggle-opt" data-tab="diff">Diferenças</div>
        </div>

        <table class="compare-table fade-up">
            <thead>
                <tr>
                    <th style="width:55%">Função</th>
                    <th>Gratuito</th>
                    <th style="color:var(--bronze)">Plus</th>
                </tr>
            </thead>
            <tbody id="compareBody">
                <tr data-type="all">
                    <td>Agenda digital</td>
                    <td><span class="check-yes">✓</span></td>
                    <td><span class="check-yes">✓</span></td>
                </tr>
                <tr data-type="all">
                    <td>Dashboard financeiro</td>
                    <td><span class="check-yes">✓</span></td>
                    <td><span class="check-yes">✓</span></td>
                </tr>
                <tr data-type="all">
                    <td>Anamnese digital</td>
                    <td><span class="check-yes">✓</span></td>
                    <td><span class="check-yes">✓</span></td>
                </tr>
                <tr data-type="all">
                    <td>Lixeira blindada</td>
                    <td><span class="check-yes">✓</span></td>
                    <td><span class="check-yes">✓</span></td>
                </tr>
                <tr data-type="diff">
                    <td>Número de profissionais</td>
                    <td style="color:rgba(253,251,247,0.35); font-size:13px">1</td>
                    <td style="color:var(--bronze); font-weight:600">até 5</td>
                </tr>
                <tr data-type="diff">
                    <td>WhatsApp automático</td>
                    <td><span class="check-no">✕</span></td>
                    <td><span class="check-yes">✓</span></td>
                </tr>
                <tr data-type="diff">
                    <td>Consultor IA de marketing</td>
                    <td><span class="check-no">✕</span></td>
                    <td><span class="check-yes">✓</span></td>
                </tr>
                <tr data-type="diff">
                    <td>Suporte prioritário</td>
                    <td><span class="check-no">✕</span></td>
                    <td><span class="check-yes">✓</span></td>
                </tr>
            </tbody>
        </table>
    </section>

    <!-- FOOTER -->
    <footer>
        <div>
            <p class="logo-text">Agenda<span>Lash</span></p>
            <p style="margin-top:6px; font-size:12px; color:rgba(253,251,247,0.18);">Pagamentos via Asaas · Ambiente seguro</p>
        </div>
        <p>Desenvolvido por <a href="https://cauavitorio-dev.vercel.app/" target="_blank" style="color:var(--bronze); text-decoration:underline;">Cauã Dev</a></p>
        <p>© 2026 AgendaLash. Sua arte merece uma gestão de elite.</p>
    </footer>

    <script>
        gsap.registerPlugin(ScrollTrigger);

        // ── Custom Cursor ────────────────────────────
        const dot = document.getElementById('cursorDot');
        const ring = document.getElementById('cursorRing');
        let mouseX = 0, mouseY = 0, ringX = 0, ringY = 0;

        document.addEventListener('mousemove', e => {
            mouseX = e.clientX; mouseY = e.clientY;
            dot.style.left = mouseX + 'px';
            dot.style.top = mouseY + 'px';
        });

        function animateRing() {
            ringX += (mouseX - ringX) * 0.12;
            ringY += (mouseY - ringY) * 0.12;
            ring.style.left = ringX + 'px';
            ring.style.top = ringY + 'px';
            requestAnimationFrame(animateRing);
        }
        animateRing();

        document.querySelectorAll('a, button, .feat-cell, .plan-card, .toggle-opt').forEach(el => {
            el.addEventListener('mouseenter', () => document.body.classList.add('hovering'));
            el.addEventListener('mouseleave', () => document.body.classList.remove('hovering'));
        });

        // ── Fade-up / fade-left via ScrollTrigger ───
        gsap.utils.toArray('.fade-up').forEach(el => {
            gsap.fromTo(el,
                { opacity: 0, y: 36 },
                { opacity: 1, y: 0, duration: 0.9, ease: 'power3.out',
                  scrollTrigger: { trigger: el, start: 'top 88%', once: true } }
            );
        });
        gsap.utils.toArray('.fade-left').forEach(el => {
            gsap.fromTo(el,
                { opacity: 0, x: -36 },
                { opacity: 1, x: 0, duration: 0.9, ease: 'power3.out',
                  scrollTrigger: { trigger: el, start: 'top 88%', once: true } }
            );
        });

        // ── Trigger hero animations immediately ─────
        gsap.fromTo('.hero-left .fade-up',
            { opacity: 0, y: 36 },
            { opacity: 1, y: 0, duration: 0.9, stagger: 0.12, ease: 'power3.out', delay: 0.1 }
        );
        gsap.fromTo('.hero-left .fade-left',
            { opacity: 0, x: -36 },
            { opacity: 1, x: 0, duration: 0.9, ease: 'power3.out' }
        );
        gsap.fromTo('.login-card',
            { opacity: 0, x: 40 },
            { opacity: 1, x: 0, duration: 1, ease: 'power3.out', delay: 0.3 }
        );

        // ── Counting animation for stats ─────────────
        const statsObserver = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (!entry.isIntersecting) return;
                entry.target.querySelectorAll('[data-count]').forEach(el => {
                    const target = parseInt(el.dataset.count);
                    const suffix = el.dataset.suffix || (target >= 1000 ? '+' : '%' in el.closest('.stat-item').querySelector('.stat-label').textContent ? '%' : '');
                    let current = 0;
                    const step = target / 60;
                    const timer = setInterval(() => {
                        current = Math.min(current + step, target);
                        el.textContent = Math.round(current) + (target === 80 ? '%' : target >= 1000 ? '+' : '');
                        if (current >= target) clearInterval(timer);
                    }, 16);
                });
                statsObserver.unobserve(entry.target);
            });
        }, { threshold: 0.5 });
        statsObserver.observe(document.querySelector('.stats-strip'));

        // ── Comparison table toggle ──────────────────
        const toggle = document.getElementById('compareToggle');
        const compareBody = document.getElementById('compareBody');
        let activeTab = 'all';

        toggle.addEventListener('click', e => {
            const opt = e.target.closest('.toggle-opt');
            if (!opt) return;
            activeTab = opt.dataset.tab;
            toggle.querySelectorAll('.toggle-opt').forEach(o => o.classList.toggle('active', o === opt));

            compareBody.querySelectorAll('tr').forEach(row => {
                if (activeTab === 'all') {
                    row.style.display = '';
                } else {
                    row.style.display = row.dataset.type === 'diff' ? '' : 'none';
                }
            });
        });

        // ── Plan card 3D tilt ────────────────────────
        document.querySelectorAll('.plan-card').forEach(card => {
            card.addEventListener('mousemove', e => {
                const r = card.getBoundingClientRect();
                const x = (e.clientX - r.left) / r.width - 0.5;
                const y = (e.clientY - r.top) / r.height - 0.5;
                gsap.to(card, { rotateY: x * 10, rotateX: -y * 10, transformPerspective: 900, ease: 'power2.out', duration: 0.4 });
            });
            card.addEventListener('mouseleave', () => {
                gsap.to(card, { rotateY: 0, rotateX: 0, ease: 'power3.out', duration: 0.8 });
            });
        });

        // ── Nav shrink on scroll ─────────────────────
        ScrollTrigger.create({
            start: 'top -60',
            onUpdate: self => {
                document.getElementById('mainNav').style.boxShadow =
                    self.progress > 0 ? '0 4px 24px rgba(58,35,24,0.08)' : '';
            }
        });
    </script>

</body>
</html>