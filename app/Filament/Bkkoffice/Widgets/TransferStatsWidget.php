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
        $pending   = MoneyTransferInvoice::where('status', 'pending_bkk_approval')->where('transfer_type', 'Transfer-OUT')->whereDate('created_at', today())->count();
        $accepted  = MoneyTransferInvoice::where('status', 'accepted_bkk')->where('transfer_type', 'Transfer-OUT')->whereDate('created_at', today())->count();
        $completed = MoneyTransferInvoice::where('status', 'completed')->where('transfer_type', 'Transfer-OUT')->whereDate('created_at', today())->count();
        $rejected  = MoneyTransferInvoice::where('status', 'Rejected')->where('transfer_type', 'Transfer-OUT')->whereDate('created_at', today())->count();

        $totalNet = MoneyTransferInvoice::where('status', 'completed')
            ->where('transfer_type', 'Transfer-OUT')
            ->whereDate('created_at', today())
            ->sum('net_amount');

        return [
            Stat::make('⏳ ' .__('message.Pending Approval'), $pending)
                ->description(__('message.Awaiting your action'))
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->chart([1, 2, $pending]),

            Stat::make('✅ ' .__('message.Accepted Today'), $accepted)
                ->description(__('message.Processing in progress'))
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('✔  ' .__('message.Completed Today'), $completed)
                ->description('THB ' . number_format($totalNet, 2))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),

            Stat::make('❌  ' .__('message.Rejected Today'), $rejected)
                ->description(__('message.Review if needed'))
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
        ];
    }
}