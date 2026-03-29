<?php

namespace App\Filament\Bkkoffice\Widgets;

use App\Models\MoneyTransferInvoice;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TransferInStatsWidget extends BaseWidget
{
    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $pending   = MoneyTransferInvoice::where('status', 'pending_bkk_approval')->where('transfer_type', 'Transfer-IN')->count();
        $accepted  = MoneyTransferInvoice::where('status', 'accepted_bkk')->where('transfer_type', 'Transfer-IN')->whereDate('created_at', today())->count();
        $completed = MoneyTransferInvoice::where('status', 'completed')->where('transfer_type', 'Transfer-IN')->whereDate('created_at', today())->count();
        $rejected  = MoneyTransferInvoice::where('status', 'Rejected')->where('transfer_type', 'Transfer-IN')->whereDate('created_at', today())->count();

        $totalNet = MoneyTransferInvoice::where('status', 'completed')
            ->where('transfer_type', 'Transfer-IN')
            ->whereDate('created_at', today())
            ->sum('net_amount');

        return [
            Stat::make('⬇ ' . __('message.Pending') . ' ' . __('message.Transfer-IN'), $pending)
                ->description(__('message.Awaiting your action'))
                ->descriptionIcon('heroicon-m-clock')
                ->color('success'),

            Stat::make('✅ ' . __('message.Accepted Today') . ' ' . __('message.Transfer-IN'), $accepted)
                ->description(__('message.Processing in progress'))
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('primary'),

            Stat::make('✔ ' . __('message.Completed Today') . ' ' . __('message.Transfer-IN'), $completed)
                ->description(number_format($totalNet, 2))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('info'),

            Stat::make('❌ ' . __('message.Rejected Today') . ' ' . __('message.Transfer-IN'), $rejected)
                ->description(__('message.Review if needed'))
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
        ];
    }
}