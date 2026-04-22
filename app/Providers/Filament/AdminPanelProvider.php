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
use Filament\Support\Enums\MaxWidth; 

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
            
            // TIPOGRAFIA EXATA DA SUA LANDING PAGE
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
                    </style>
                '
            )

            // --- CONFIGURAÇÃO DO SAAS (TENANCY) ---
            ->tenant(\App\Models\Studio::class)
            ->tenantProfile(EditStudioProfile::class)

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