<?php

namespace App\Filament\Teller\Exports;

use App\Models\MoneyTransferInvoice;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Illuminate\Support\Carbon;

/**
 * TransferOutExport
 * ─────────────────
 * Used by ExportAction in TransferOutResource.
 * Exports all currently-filtered Transfer-OUT records
 * to Excel (.xlsx) with professional formatting.
 *
 * Install package first:
 *   composer require pxlrbt/filament-excel
 */
class TransferOutExport extends ExcelExport
{
    /**
     * Sheet / file name includes today's date for easy filing
     */
    public function getFileName(): string
    {
        return 'Transfer-OUT_' . Carbon::today()->format('Y-m-d');
    }

    /**
     * Column definitions — all translated via __('message.key')
     */
    public function getColumns(): array
    {
        return [
            Column::make('invoice_number')
                ->heading(__('message.Invoice Number')),

            Column::make('customer_name')
                ->heading(__('message.Customer Name')),

            Column::make('phone')
                ->heading(__('message.Phone')),

            Column::make('bank_name')
                ->heading(__('message.Bank Name')),

            Column::make('acc_name')
                ->heading(__('message.Account Name')),

            Column::make('acc_number')
                ->heading(__('message.Account Number')),

            Column::make('currency')
                ->heading(__('message.Currency')),

            Column::make('entered_amount')
                ->heading(__('message.Entered Amount')),

            Column::make('trf_fee_in_persentage')
                ->heading(__('message.Transfer Fee %')),

            Column::make('trf_fee')
                ->heading(__('message.Transfer Fee')),

            Column::make('net_amount')
                ->heading(__('message.Net Amount')),

            Column::make('status')
                ->heading(__('message.Status'))
                ->formatStateUsing(fn ($state) => match ($state) {
                    'pending_bkk_approval' => __('message.Pending'),
                    'accepted_bkk'         => __('message.Accepted'),
                    'completed'            => __('message.Completed'),
                    'Rejected'             => __('message.Rejected'),
                    'cancelled'            => __('message.Cancelled'),
                    default                => $state,
                }),

            Column::make('reject_reason')
                ->heading(__('message.Reject Reason')),

            Column::make('createdBy')
                ->heading(__('message.Created By')),

            Column::make('created_at')
                ->heading(__('message.Created At'))
                ->formatStateUsing(fn ($state) => $state
                    ? Carbon::parse($state)->format('d M Y H:i')
                    : '—'
                ),

            Column::make('updated_at')
                ->heading('Updated At')
                ->formatStateUsing(fn ($state) => $state
                    ? Carbon::parse($state)->format('d M Y H:i')
                    : '—'
                ),
        ];
    }
}