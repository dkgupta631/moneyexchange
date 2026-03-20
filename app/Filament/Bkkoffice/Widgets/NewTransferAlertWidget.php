<?php

namespace App\Filament\Bkkoffice\Widgets;

use App\Models\MoneyTransferInvoice;
use Filament\Widgets\Widget;
use Filament\Notifications\Notification;
use Livewire\Attributes\On;

class NewTransferAlertWidget extends Widget
{
    protected static string $view = 'filament.bkkoffice.widgets.new-transfer-alert';

    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = 'full';

    public bool $showPopup = false;
    public ?array $latestTransfer = null;
    public int $lastKnownCount = 0;
    public int $pendingCount = 0;

    public function mount(): void
    {
        $this->lastKnownCount = cache()->get('bkk_transfer_count_' . auth()->id(), 0);
        $this->pendingCount   = $this->getPendingCount();
    }

    public function getPendingCount(): int
    {
        return MoneyTransferInvoice::where('status', 'pending_bkk_approval')
            ->where('transfer_type', 'Transfer-OUT')
            ->count();
    }

    // Called by JS polling every 8 seconds
    public function checkNewTransfers(): void
    {
        $currentCount = $this->getPendingCount();
        $this->pendingCount = $currentCount;

        if ($currentCount > $this->lastKnownCount) {
            // New transfer arrived!
            $latest = MoneyTransferInvoice::where('status', 'pending_bkk_approval')
                ->where('transfer_type', 'Transfer-OUT')
                ->latest()
                ->first();

            if ($latest) {
                $this->latestTransfer = [
                    'id'              => $latest->id,
                    'invoice_number'  => $latest->invoice_number,
                    'customer_name'   => $latest->customer_name,
                    'bank_name'       => $latest->bank_name,
                    'amount'          => number_format($latest->entered_amount, 2),
                    'currency'        => $latest->currency,
                    'net_amount'      => number_format($latest->net_amount, 2),
                    'created_at'      => $latest->created_at->format('d M Y H:i:s'),
                ];

                $this->showPopup = true;

                // Trigger browser voice + visual notification
                $this->dispatch('new-transfer-arrived', transfer: $this->latestTransfer);

                // Filament toast notification
                Notification::make()
                    ->title('🔔 New Transfer-OUT Request!')
                    ->body("From {$latest->customer_name} — {$latest->currency} {$this->latestTransfer['amount']}")
                    ->warning()
                    ->persistent()
                    ->send();
            }

            $this->lastKnownCount = $currentCount;
            cache()->put('bkk_transfer_count_' . auth()->id(), $currentCount, 3600);
        }
    }

    public function acceptTransfer(int $id): void
    {
        $record = MoneyTransferInvoice::find($id);
        if ($record) {
            $record->update(['status' => 'accepted_bkk']);

            Notification::make()
                ->title('✅ Transfer Accepted')
                ->body("Invoice #{$record->invoice_number} accepted successfully.")
                ->success()
                ->send();
        }

        $this->closePopup();
        $this->pendingCount = $this->getPendingCount();
    }

    public function rejectTransfer(int $id): void
    {
        $record = MoneyTransferInvoice::find($id);
        if ($record) {
            $record->update(['status' => 'Rejected']);

            Notification::make()
                ->title('❌ Transfer Rejected')
                ->body("Invoice #{$record->invoice_number} rejected.")
                ->danger()
                ->send();
        }

        $this->closePopup();
        $this->pendingCount = $this->getPendingCount();
    }

    public function closePopup(): void
    {
        $this->showPopup    = false;
        $this->latestTransfer = null;
        $this->lastKnownCount = $this->getPendingCount();
        cache()->put('bkk_transfer_count_' . auth()->id(), $this->lastKnownCount, 3600);
    }
}