<?php

namespace App\Filament\Teller\Widgets;

use App\Models\MoneyTransferInvoice;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class TransferOutStatsWidget extends BaseWidget
{
    /**
     * Auto-refresh every 8 seconds for real-time updates
     */
    protected static ?string $pollingInterval = '5s';

    protected function getStats(): array
    {
        $today = Carbon::today();

        $pending = MoneyTransferInvoice::query()
            ->where('transfer_type', 'Transfer-OUT')
            ->whereDate('created_at', $today)
            ->where('status', 'pending_bkk_approval')
            ->count();

        $accepted = MoneyTransferInvoice::query()
            ->where('transfer_type', 'Transfer-OUT')
            ->whereDate('created_at', $today)
            ->where('status', 'accepted_bkk')
            ->count();

        $completed = MoneyTransferInvoice::query()
            ->where('transfer_type', 'Transfer-OUT')
            ->whereDate('created_at', $today)
            ->where('status', 'completed')
            ->count();

        $completedAmount = MoneyTransferInvoice::query()
            ->where('transfer_type', 'Transfer-OUT')
            ->whereDate('created_at', $today)
            ->where('status', 'completed')
            ->sum('net_amount');

        $rejected = MoneyTransferInvoice::query()
            ->where('transfer_type', 'Transfer-OUT')
            ->whereDate('created_at', $today)
            ->where('status', 'Rejected')
            ->count();

        return [
            Stat::make(
                '⏳ ' . __('message.Pending') . ' ' . __('message.Transfer-OUT'),
                $pending
            )
                ->description(__('message.Awaiting your action'))
                ->descriptionIcon('heroicon-m-clock')
                ->color($pending > 0 ? 'warning' : 'gray')
                ->chart($this->getRecentCounts('pending_bkk_approval')),

            Stat::make(
                '✅ ' . __('message.Accepted') . ' ' . __('message.Transfer-OUT'),
                $accepted
            )
                ->description(__('message.Processing in progress'))
                ->descriptionIcon('heroicon-m-check-circle')
                ->color($accepted > 0 ? 'info' : 'gray')
                ->chart($this->getRecentCounts('accepted_bkk')),

            Stat::make(
                '✔️ ' . __('message.Completed') . ' ' . __('message.Transfer-OUT'),
                $completed
            )
                ->description('THB ' . number_format($completedAmount, 2))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->chart($this->getRecentCounts('completed')),

            Stat::make(
                '✖️ ' . __('message.Rejected') . ' ' . __('message.Transfer-OUT'),
                $rejected
            )
                ->description(__('message.Review if needed'))
                ->descriptionIcon('heroicon-m-x-circle')
                ->color($rejected > 0 ? 'danger' : 'gray')
                ->chart($this->getRecentCounts('Rejected')),
        ];
    }

    /**
     * Generate a mini 7-point chart for last 7 days counts
     */
    private function getRecentCounts(string $status): array
    {
        $counts = [];
        for ($i = 6; $i >= 0; $i--) {
            $counts[] = MoneyTransferInvoice::query()
                ->where('transfer_type', 'Transfer-OUT')
                ->whereDate('created_at', Carbon::today()->subDays($i))
                ->where('status', $status)
                ->count();
        }
        return $counts;
    }
}