<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Filament\Pages\Tenancy\EditStudioProfile;
use Filament\Navigation\MenuItem;
use App\Filament\Billing\AsaasBillingProvider;


class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->userMenuItems([
                'logout' => MenuItem::make()
                    ->label('Sair da Agenda')
                    ->url(fn(): string => route('sair.agora')),
            ])
            ->registration() // Permite criar conta
            ->authGuard('web')

            // --- CONFIGURAÇÃO DE APARÊNCIA (Padrão Elite) ---
            ->colors([
                'primary' => [
                    50 => '#fdf8f6',
                    100 => '#f2e8e5',
                    200 => '#eaddd7',
                    300 => '#e0cec7',
                    400 => '#c28e64', 
                    500 => '#844d36', 
                    600 => '#6b3728', 
                    700 => '#4a261c',
                    800 => '#3a2318',
                    900 => '#271c19',
                    950 => '#1c1412',
                ],
                'gray' => Color::Stone,
                'danger' => Color::Rose,
                'info' => Color::Blue,
                'success' => Color::Emerald,
                'warning' => Color::Amber,
            ])
            
            // TIPOGRAFIA 
            ->font('DM Sans')

            // LAYOUT MODERNO
            ->maxContentWidth('full') // Usa a largura total para caber a agenda melhor

            // --- CSS INJETADO (Bordas e Blur) SEM PRECISAR DE VITE BUILD ---
            ->renderHook(
                \Filament\View\PanelsRenderHook::HEAD_END,
                fn (): string => '
                    <style>
                        /* Arredondamento Global e Estilo de Elite */
                        :root {
                            --fi-border-radius-xl: 1.5rem; 
                            --fi-border-radius-lg: 1.25rem;   
                            --fi-border-radius-md: 0.75rem; 
                        }
                        
                        /* Navbar flutuante com blur */
                        .fi-topbar {
                            background: rgba(253, 251, 247, 0.85) !important;
                            backdrop-filter: blur(12px) !important;
                            -webkit-backdrop-filter: blur(12px) !important;
                            border-bottom: 1px solid rgba(58, 35, 24, 0.05) !important;
                        }
                        
                        .dark .fi-topbar {
                            background: rgba(24, 24, 27, 0.85) !important;
                            border-bottom: 1px solid rgba(255, 255, 255, 0.05) !important;
                        }

                        .btn-upgrade-header {
                            background: linear-gradient(135deg, #c28e64 0%, #844d36 100%);
                            color: white;
                            font-weight: bold;
                            padding: 0.4rem 1rem;
                            border-radius: 9999px;
                            display: flex;
                            align-items: center;
                            gap: 0.5rem;
                            font-size: 0.875rem;
                            transition: all 0.2s ease-in-out;
                            box-shadow: 0 4px 6px -1px rgba(194, 142, 100, 0.3);
                            text-decoration: none;
                        }
                        .btn-upgrade-header:hover {
                            transform: translateY(-1px);
                            box-shadow: 0 6px 10px -1px rgba(194, 142, 100, 0.4);
                        }
                    </style>
                '
            )
            ->renderHook(
                \Filament\View\PanelsRenderHook::GLOBAL_SEARCH_BEFORE,
                fn (): string => \Filament\Facades\Filament::getTenant() && \Filament\Facades\Filament::getTenant()->plan_type !== 'plus'
                    ? '<a href="/admin/meu-plano" class="btn-upgrade-header" style="margin-right: 1rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 1rem; height: 1rem;">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                        </svg>
                        Upgrade
                      </a>'
                    : ''
            )

            // --- CONFIGURAÇÃO DO SAAS (TENANCY) ---
            ->tenant(\App\Models\Studio::class)
            ->tenantRegistration(\App\Filament\Pages\Tenancy\RegisterStudio::class)
            ->tenantMenuItems([
                'register' => MenuItem::make()->icon('heroicon-o-cog-6-tooth'),
            ])
            ->tenantBillingProvider(new AsaasBillingProvider())
            ->tenantMiddleware([\App\Http\Middleware\ApplyTenantScopes::class], isPersistent: true)

            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                //
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);

    }
}