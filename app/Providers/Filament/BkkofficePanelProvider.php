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
use Filament\Navigation\MenuItem;
// use App\Filament\Pages\Auth\ChangePassword;
use Filament\Forms\Components\ColorPicker;

class BkkofficePanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('bkkoffice')
            ->path('bkkoffice')
            ->authGuard('bkkoffice') 
            ->login(CustomLogin::class)
            ->profile()
            ->colors([
                'primary' => Color::Purple,
            ])
             // Created by DK START
            ->maxContentWidth('full')
            // ->viteTheme('resources/css/app.css')
            // ->topNavigation()
            ->font('Poppins')
            // ->favicon(fn () => view('filament.faviconlogo'))
            ->favicon(url('website/assets/logo/logo2.png'))
            ->brandLogo(fn () => view('filament.logo')) // Dashboard sidebar logo
            ->sidebarCollapsibleOnDesktop()
            ->userMenuItems([
                // 'profile' => MenuItem::make('profile')
                //     ->label(fn (): string => 'Profile')
                //     ->url(fn (): string => Profile::getUrl()),

                // 'change-password' => MenuItem::make('Change Password')
                //     ->url(fn (): string => ChangePassword::getUrl())
                //     ->label(fn (): string => 'Change Password')
                //     ->icon('heroicon-s-lock-closed'),
            ])
            // ->databaseNotifications()
            // ->databaseNotificationsPolling('30s')
              // Created by DK END
              
            // ->discoverResources(in: app_path('Filament/Bkkoffice/Resources'), for: 'App\\Filament\\Bkkoffice\\Resources')
            // ->discoverPages(in: app_path('Filament/Bkkoffice/Pages'), for: 'App\\Filament\\Bkkoffice\\Pages')
            // ->pages([
            //     Pages\Dashboard::class,
            // ])
            // ->discoverWidgets(in: app_path('Filament/Bkkoffice/Widgets'), for: 'App\\Filament\\Bkkoffice\\Widgets')
            // ->widgets([
            //     Widgets\AccountWidget::class,
            //     Widgets\FilamentInfoWidget::class,
            // ])
            ->discoverResources(in: app_path('Filament/Bkkoffice/Resources'), for: 'App\\Filament\\Bkkoffice\\Resources')
            ->discoverPages(in: app_path('Filament/Bkkoffice/Pages'), for: 'App\\Filament\\Bkkoffice\\Pages')
            ->pages([
                \App\Filament\Bkkoffice\Pages\Dashboard::class,  // ← use custom dashboard
            ])
            ->discoverWidgets(in: app_path('Filament/Bkkoffice/Widgets'), for: 'App\\Filament\\Bkkoffice\\Widgets')
            ->widgets([
                \Filament\Widgets\AccountWidget::class,
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
