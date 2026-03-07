<?php

namespace App\Providers\Filament;

use App\Filament\Helper\CustomLogin;
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

use Filament\Navigation\MenuItem;
// use App\Filament\Pages\Auth\ChangePassword;
use Filament\Forms\Components\ColorPicker;


class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
       
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->profile()
            ->colors([
                'primary' => Color::Blue,
                'sidebar' => [
                    'background' => '#053cb1', // Sidebar bg color
                    'text' => '#F3F4F6',       // Sidebar text color
                ],
                'header' => [
                    'background' => '#0f1ebe', // Header bg color
                ],
                'body' => [
                    'background' => '#F9FAFB', // Main body background
                ],
            ])
            

            // Created by DK START
            ->maxContentWidth('full')
            // ->viteTheme('resources/css/app.css')
            // ->topNavigation()
            ->font('Poppins')
            // ->favicon(fn () => view('filament.faviconlogo'))
            ->favicon(url('website/assets/logo/logo.png'))
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
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
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
