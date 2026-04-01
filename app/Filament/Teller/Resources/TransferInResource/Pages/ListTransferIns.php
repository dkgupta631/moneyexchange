<?php

namespace App\Filament\Teller\Resources\TransferInResource\Pages;

use App\Filament\Teller\Resources\TransferInResource;
use App\Models\MoneyTransferInvoice;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Carbon;

class ListTransferIns extends ListRecords
{
    protected static string $resource = TransferInResource::class;

    // protected static string $view = 'filament.teller.pages.list-transfer-ins';

    // ── Livewire public props (synced to Alpine via data-* in blade) ────────
    public int $pendingCount  = 0;
    public int $acceptedCount = 0;

    public function getBreadcrumb(): string
    {
        return __('message.List');
    }

    public function getTitle(): string
    {
        return __('message.Transfer-IN Requests');
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function mount(): void
    {
        parent::mount();
        $this->refreshCounts();
    }

    // ─── Called by wire:poll.8000ms in blade ────────────────────────────────
    public function checkNewNotifications(): void
    {
        $this->refreshCounts();

        // Find the latest completed Transfer-IN today
        $completed = MoneyTransferInvoice::query()
            ->where('transfer_type', 'Transfer-IN')
            ->whereDate('created_at', Carbon::today())
            ->where('status', 'completed')
            ->latest('updated_at')
            ->first();

        if (! $completed) {
            return;
        }

        // Dispatch to Alpine window event — Alpine's localStorage seen-set
        // prevents the popup from firing more than once per record id
        $this->dispatch('transfer-in-new-notification', record: [
            'id'             => $completed->id,
            'invoice_number' => $completed->invoice_number,
            'customer_name'  => $completed->customer_name,
            'bank_name'      => $completed->bank_name,
            'acc_name'       => $completed->acc_name,
            'acc_number'     => $completed->acc_number,
            'entered_amount' => $completed->entered_amount,
            'net_amount'     => $completed->net_amount,
            'currency'       => $completed->currency ?? '$',
            'status'         => $completed->status,
            'popup_type'     => 'completed',
        ]);
    }

    // ─── Helper ─────────────────────────────────────────────────────────────
    private function refreshCounts(): void
    {
        $base = MoneyTransferInvoice::query()
            ->where('transfer_type', 'Transfer-IN')
            ->whereDate('created_at', Carbon::today());

        $this->pendingCount  = (clone $base)->where('status', 'pending_bkk_approval')->count();
        $this->acceptedCount = (clone $base)->where('status', 'accepted_bkk')->count();
    }

    /**
     * Header widgets: show the live-poll notification widget
     */
    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Teller\Widgets\TransferInLiveBanner::class,
        ];
    }
}