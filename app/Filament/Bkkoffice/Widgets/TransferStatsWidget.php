<?php

namespace App\Filament\Bkkoffice\Widgets;

use App\Models\MoneyTransferInvoice;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TransferStatsWidget extends BaseWidget
{
    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $pending   = MoneyTransferInvoice::where('status', 'pending_bkk_approval')->where('transfer_type', 'Transfer-OUT')->count();
        $accepted  = MoneyTransferInvoice::where('status', 'accepted_bkk')->where('transfer_type', 'Transfer-OUT')->count();
        $completed = MoneyTransferInvoice::where('status', 'completed')->where('transfer_type', 'Transfer-OUT')->whereDate('created_at', today())->count();
        $rejected  = MoneyTransferInvoice::where('status', 'Rejected')->where('transfer_type', 'Transfer-OUT')->whereDate('created_at', today())->count();

        $totalNet = MoneyTransferInvoice::where('status', 'completed')
            ->where('transfer_type', 'Transfer-OUT')
            ->whereDate('created_at', today())
            ->sum('net_amount');

        return [
            Stat::make('⏳ Pending Approval', $pending)
                ->description('Awaiting your action')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->chart([1, 2, $pending]),

            Stat::make('✅ Accepted Today', $accepted)
                ->description('Processing in progress')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('✔ Completed Today', $completed)
                ->description('THB ' . number_format($totalNet, 2))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),

            Stat::make('❌ Rejected Today', $rejected)
                ->description('Review if needed')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
        ];
    }
}