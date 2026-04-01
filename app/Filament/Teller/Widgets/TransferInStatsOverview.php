<?php

namespace App\Filament\Teller\Widgets;

use App\Models\MoneyTransferInvoice;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class TransferInStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getPollingInterval(): ?string
    {
        return '8s';
    }

    protected function getStats(): array
    {
        $base = MoneyTransferInvoice::query()
            ->where('transfer_type', 'Transfer-IN')
            ->whereDate('created_at', Carbon::today());

        $pending  = (clone $base)->where('status', 'pending_bkk_approval')->count();
        $accepted = (clone $base)->where('status', 'accepted_bkk')->count();
        $completed = (clone $base)->where('status', 'completed')->count();
        $rejected = (clone $base)->where('status', 'Rejected')->count();

        $completedTotal = (clone $base)->where('status', 'completed')->sum('net_amount');
        $completedFee   = (clone $base)->where('status', 'completed')->sum('trf_fee');

        return [
            Stat::make(
                '⏳ ' . __('message.Pending') . ' ' . __('message.Transfer-IN'),
                $pending
            )
                ->description(__('message.Awaiting your action'))
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->chart([1, 2, $pending]),

            Stat::make(
                '✅ ' . __('message.Accepted') . ' ' . __('message.Transfer-IN'),
                $accepted
            )
                ->description(__('message.Processing in progress'))
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('info')
                ->chart([0, 1, $accepted]),

            Stat::make(
                '✔️ ' . __('message.Completed') . ' ' . __('message.Transfer-IN'),
                $completed
            )
                ->description(__('message.Total') . ': ' . number_format($completedTotal, 2) . ' | ' . __('message.Fee') . ': ' . number_format($completedFee, 2) . ' 💵')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success')
                ->chart([0, 1, $completed]),

            Stat::make(
                '✖️ ' . __('message.Rejected') . ' ' . __('message.Transfer-IN'),
                $rejected
            )
                ->description(__('message.Review if needed'))
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger')
                ->chart([0, 0, $rejected]),
        ];
    }
}