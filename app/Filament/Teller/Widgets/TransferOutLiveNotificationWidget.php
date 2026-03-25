<?php

namespace App\Filament\Teller\Widgets;

use App\Models\MoneyTransferInvoice;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;

/**
 * TransferOutLiveNotificationWidget
 * ───────────────────────────────────
 * FIX: In Filament v3 / Livewire 3, $this->dispatch() dispatches a
 * Livewire event, NOT a browser JS event.
 *
 * To send data to the browser/Alpine, we use $this->js() to execute
 * inline JavaScript directly after every poll. This calls a global
 * function `tellerReceive(data)` that Alpine registers on window.
 *
 * This is 100% reliable in Filament v3 + Livewire 3 on Laravel 12.
 */
class TransferOutLiveNotificationWidget extends Widget
{
    protected static string $view = 'filament.teller.widgets.transfer-out-live-notification';

    /**
     * Livewire polls this widget every 5 seconds.
     * The widget's own $pollingInterval triggers refreshData() automatically.
     */
    protected static ?string $pollingInterval = '5s';

    protected int | string | array $columnSpan = 'full';

    public array $latestAccepted  = [];
    public array $latestCompleted = [];
    public int   $pendingCount    = 0;

    public function mount(): void
    {
        $this->loadData();
    }

    /**
     * Called automatically by Livewire every 5s via pollingInterval.
     * After loading data, pushes it to the browser via inline JS.
     */
    public function refreshData(): void
    {
        $this->loadData();

        // ── Push to browser via inline JS execution ─────────────────────
        // $this->js() runs JavaScript in the browser after Livewire responds.
        // tellerReceive() is registered on window by the Alpine component.
        $payload = json_encode([
            'accepted'     => $this->latestAccepted,
            'completed'    => $this->latestCompleted,
            'pendingCount' => $this->pendingCount,
        ], JSON_UNESCAPED_UNICODE);

        $this->js("if(typeof window.tellerReceive==='function'){window.tellerReceive({$payload});}");
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

        // Completed: append '_c' suffix so the ID never collides with accepted
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
}