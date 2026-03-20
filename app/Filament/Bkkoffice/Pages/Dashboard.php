<?php

namespace App\Filament\Bkkoffice\Pages;

use App\Filament\Bkkoffice\Widgets\TransferStatsWidget;
use App\Filament\Bkkoffice\Widgets\NewTransferAlertWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    public function getWidgets(): array
    {
        return [
            NewTransferAlertWidget::class,
            TransferStatsWidget::class,
        ];
    }

    public function getColumns(): int | string | array
    {
        return 1;
    }
}