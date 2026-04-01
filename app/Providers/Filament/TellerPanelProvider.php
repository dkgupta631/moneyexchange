<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

use App\Filament\Helper\CustomLogin;
use App\Filament\Teller\Pages\TellerDashboard;
use App\Filament\Teller\Widgets\TransferInLiveBanner;
use App\Filament\Teller\Widgets\TransferInStatsOverview;

class TellerPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('teller')
            ->path('teller')
            ->authGuard('teller')
            ->login(CustomLogin::class)
            ->profile()
            ->colors([
                'primary' => Color::Purple,
            ])
            // ─── Layout / Branding ────────────────────────────────────────
            ->maxContentWidth('full')
            ->font('Poppins')
            ->favicon(url('website/assets/logo/logo2.png'))
            ->brandLogo(fn () => view('filament.logo'))
            ->brandName('G+ Services — Teller')
            ->sidebarCollapsibleOnDesktop()
            // ─── Dashboard ────────────────────────────────────────────────
            ->pages([
                TellerDashboard::class,
            ])
            // ─── Resources ────────────────────────────────────────────────
            ->discoverResources(
                in: app_path('Filament/Teller/Resources'),
                for: 'App\\Filament\\Teller\\Resources'
            )
            ->discoverPages(
                in: app_path('Filament/Teller/Pages'),
                for: 'App\\Filament\\Teller\\Pages'
            )
            // ─── Widgets ─────────────────────────────────────────────────
            ->discoverWidgets(
                in: app_path('Filament/Teller/Widgets'),
                for: 'App\\Filament\\Teller\\Widgets'
            )
            ->widgets([
                TransferInLiveBanner::class,
                TransferInStatsOverview::class,
            ])
            // ─── Middleware ───────────────────────────────────────────────
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