<?php

namespace App\Filament\Teller\Widgets;

use App\Models\MoneyTransferInvoice;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;

class TransferInLiveBanner extends Widget
{
    protected static string $view = 'filament.teller.widgets.transfer-in-live-banner';

    protected static ?int $sort = 0;

    protected static bool $isLazy = false;

    public int $pendingCount = 0;
    public int $acceptedCount = 0;
    public string $lastChecked = '';

    protected function getPollingInterval(): ?string
    {
        return '8s';
    }

    public function mount(): void
    {
        $this->refresh();
    }

    public function refresh(): void
    {
        $base = MoneyTransferInvoice::query()
            ->where('transfer_type', 'Transfer-IN')
            ->whereDate('created_at', Carbon::today());

        $this->pendingCount  = (clone $base)->where('status', 'pending_bkk_approval')->count();
        $this->acceptedCount = (clone $base)->where('status', 'accepted_bkk')->count();
        $this->lastChecked   = now()->format('H:i:s');
    }
}