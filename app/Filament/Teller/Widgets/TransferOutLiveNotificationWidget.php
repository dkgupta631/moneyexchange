<?php

namespace App\Filament\Teller\Widgets;

use App\Models\MoneyTransferInvoice;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;

class TransferOutLiveNotificationWidget extends Widget
{
    protected static string $view = 'filament.teller.widgets.transfer-out-live-notification';

    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = 'full';

    public array  $latestAccepted   = [];
    public array  $latestCompleted  = [];
    public int    $pendingCount     = 0;
    public int    $lastKnownAccepted   = 0;
    public int    $lastKnownCompleted  = 0;

    public function mount(): void
    {
        $this->lastKnownAccepted  = (int) cache()->get($this->cacheKeyAccepted(),  0);
        $this->lastKnownCompleted = (int) cache()->get($this->cacheKeyCompleted(), 0);
        $this->loadData();
    }

    // ── called by wire:poll every 5 s ────────────────────────────────────
    public function checkNewNotifications(): void
    {
        $this->loadData();

        $currentAccepted  = count($this->latestAccepted);
        $currentCompleted = count($this->latestCompleted);

        // ── new ACCEPTED records? ─────────────────────────────────────────
        if ($currentAccepted > $this->lastKnownAccepted) {
            // find only truly new ones (by index offset)
            $newAccepted = array_slice($this->latestAccepted, 0,
                $currentAccepted - $this->lastKnownAccepted);

            foreach ($newAccepted as $record) {
                $this->dispatch('teller-new-notification', record: $record);
            }

            $this->lastKnownAccepted = $currentAccepted;
            cache()->put($this->cacheKeyAccepted(), $currentAccepted, 3600);
        }

        // ── new COMPLETED records? ────────────────────────────────────────
        if ($currentCompleted > $this->lastKnownCompleted) {
            $newCompleted = array_slice($this->latestCompleted, 0,
                $currentCompleted - $this->lastKnownCompleted);

            foreach ($newCompleted as $record) {
                $this->dispatch('teller-new-notification', record: $record);
            }

            $this->lastKnownCompleted = $currentCompleted;
            cache()->put($this->cacheKeyCompleted(), $currentCompleted, 3600);
        }
    }

    private function loadData(): void
    {
        $today = Carbon::today();

        $this->pendingCount = MoneyTransferInvoice::query()
            ->where('transfer_type', 'Transfer-OUT')
            ->whereDate('created_at', $today)
            ->whereIn('status', ['pending_bkk_approval', 'accepted_bkk'])
            ->count();

        $this->latestAccepted = MoneyTransferInvoice::query()
            ->where('transfer_type', 'Transfer-OUT')
            ->whereDate('created_at', $today)
            ->where('status', 'accepted_bkk')
            ->orderByDesc('updated_at')
            ->limit(20)
            ->get()
            ->map(fn($r) => [
                'id'             => (string) $r->id,
                'popup_type'     => 'accepted',
                'invoice_number' => (string) ($r->invoice_number ?? ''),
                'customer_name'  => (string) ($r->customer_name  ?? ''),
                'bank_name'      => (string) ($r->bank_name      ?? ''),
                'acc_name'       => (string) ($r->acc_name       ?? ''),
                'acc_number'     => (string) ($r->acc_number     ?? ''),
                'currency'       => (string) ($r->currency       ?? 'THB'),
                'entered_amount' => (float)  ($r->entered_amount ?? 0),
                'net_amount'     => (float)  ($r->net_amount     ?? 0),
            ])
            ->toArray();

        $this->latestCompleted = MoneyTransferInvoice::query()
            ->where('transfer_type', 'Transfer-OUT')
            ->whereDate('created_at', $today)
            ->where('status', 'completed')
            ->orderByDesc('updated_at')
            ->limit(20)
            ->get()
            ->map(fn($r) => [
                'id'             => (string) $r->id . '_c',
                'popup_type'     => 'completed',
                'invoice_number' => (string) ($r->invoice_number ?? ''),
                'customer_name'  => (string) ($r->customer_name  ?? ''),
                'bank_name'      => (string) ($r->bank_name      ?? ''),
                'acc_name'       => (string) ($r->acc_name       ?? ''),
                'acc_number'     => (string) ($r->acc_number     ?? ''),
                'currency'       => (string) ($r->currency       ?? 'THB'),
                'entered_amount' => (float)  ($r->entered_amount ?? 0),
                'net_amount'     => (float)  ($r->net_amount     ?? 0),
            ])
            ->toArray();
    }

    private function cacheKeyAccepted(): string
    {
        return 'teller_accepted_count_' . auth()->id();
    }

    private function cacheKeyCompleted(): string
    {
        return 'teller_completed_count_' . auth()->id();
    }
}