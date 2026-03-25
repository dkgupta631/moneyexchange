<?php

namespace App\Filament\Teller\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class TellerDashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?int $navigationSort = 0;

    public function getTitle(): string
    {
        return __('message.Dashboard');
    }

    public static function getNavigationLabel(): string
    {
        return __('message.Dashboard');
    }

    /**
     * Widgets displayed on the teller dashboard
     */
    public function getWidgets(): array
    {
        return [
            \App\Filament\Teller\Widgets\TransferOutLiveNotificationWidget::class,
            \App\Filament\Teller\Widgets\TransferOutStatsWidget::class,
        ];
    }

    public function getColumns(): int | string | array
    {
        return 4;
    }
}