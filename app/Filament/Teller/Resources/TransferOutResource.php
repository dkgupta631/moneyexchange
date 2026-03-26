<?php

namespace App\Filament\Teller\Resources;

use App\Filament\Teller\Resources\TransferOutResource\Pages;
use App\Models\MoneyTransferInvoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class TransferOutResource extends Resource
{
    protected static ?string $model = MoneyTransferInvoice::class;
    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-right';
    protected static ?int $navigationSort = 1;

    // ── Labels (i18n) ────────────────────────────────────────────────────────

    public static function getNavigationLabel(): string
    {
        return __('message.Transfer-OUT Requests');
    }

    public static function getModelLabel(): string
    {
        return __('message.Transfer-OUT');
    }

    public static function getPluralModelLabel(): string
    {
        return __('message.Transfer-OUT Requests');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('message.Transfer-OUT');
    }

    /**
     * Sidebar badge: live count of today pending + accepted
     */
    public static function getNavigationBadge(): ?string
    {
        $count = static::getEloquentQuery()
            ->whereIn('status', ['pending_bkk_approval', 'accepted_bkk'])
            ->whereDate('created_at', Carbon::today())
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $count = static::getEloquentQuery()
            ->whereIn('status', ['pending_bkk_approval', 'accepted_bkk'])
            ->whereDate('created_at', Carbon::today())
            ->count();

        return $count > 0 ? 'warning' : 'success';
    }

    // ── Base Query: Transfer-OUT, today only ─────────────────────────────────

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('transfer_type', 'Transfer-OUT')
            ->whereDate('created_at', Carbon::today())
            ->orderBy('id', 'desc')
            ->latest();
    }

    // ── Form (view-only) ─────────────────────────────────────────────────────

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make(__('message.Transfer Details'))
                ->schema([
                    Forms\Components\TextInput::make('invoice_number')->label(__('message.Invoice Number'))->disabled(),
                    Forms\Components\TextInput::make('customer_name')->label(__('message.Customer Name'))->disabled(),
                    Forms\Components\TextInput::make('phone')->label(__('message.Phone'))->disabled(),
                    Forms\Components\TextInput::make('bank_name')->label(__('message.Bank Name'))->disabled(),
                    Forms\Components\TextInput::make('acc_name')->label(__('message.Account Name'))->disabled(),
                    Forms\Components\TextInput::make('acc_number')->label(__('message.Account Number'))->disabled(),
                    Forms\Components\TextInput::make('currency')->label(__('message.Currency'))->disabled(),
                    Forms\Components\TextInput::make('entered_amount')->label(__('message.Entered Amount'))->disabled(),
                    Forms\Components\TextInput::make('trf_fee')->label(__('message.Transfer Fee'))->disabled(),
                    Forms\Components\TextInput::make('net_amount')->label(__('message.Net Amount'))->disabled(),
                    Forms\Components\Select::make('status')
                        ->label(__('message.Status'))
                        ->options([
                            'pending_bkk_approval' => __('message.Pending'),
                            'accepted_bkk'         => __('message.Accepted'),
                            'completed'            => __('message.Completed'),
                            'Rejected'             => __('message.Rejected'),
                            'cancelled'            => __('message.Cancelled'),
                        ])->disabled(),
                    Forms\Components\Textarea::make('reject_reason')->label(__('message.Reject Reason'))->disabled(),
                ])->columns(2),
        ]);
    }

    // ── Table ────────────────────────────────────────────────────────────────

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('Serial_number')
                    ->label(__('message.Serial number'))
                    ->badge()
                    ->state(fn($column) => $column->getRowLoop()->iteration),
                TextColumn::make('created_at')
                    ->label(__('message.Time'))
                    ->dateTime('d M Y h:i')
                    ->sortable()
                    ->color('gray'),
                TextColumn::make('invoice_number')
                    ->label(__('message.Invoice Number'))
                    ->searchable()->sortable()->copyable()
                    ->weight('bold')->color('primary'),

                TextColumn::make('customer_name')
                    ->label(__('message.Customer Name'))
                    ->searchable()->sortable(),

               TextColumn::make('bank_details')
                    ->label(__('message.Bank Details'))
                    ->getStateUsing(function ($record) {
                        return "{$record->bank_name} - {$record->acc_number} - {$record->acc_name}";
                    })
                    ->searchable()
                    ->copyable()
                    ->wrap(),
                TextColumn::make('entered_amount')
                    ->label(__('message.Amount'))
                    ->formatStateUsing(function ($state, $record) {
                        return $record->currency . ' ' . $state;
                    })
                    ->sortable()
                    ->alignRight(),
                TextColumn::make('trf_fee')
                    ->label(__('message.Transfer Fee'))
                    ->formatStateUsing(function ($state, $record) {
                        return $record->currency . ' ' . $state;
                    })
                    ->sortable(),
                TextColumn::make('net_amount')
                    ->label(__('message.Net Amount'))
                    ->formatStateUsing(function ($state, $record) {
                        return $record->currency . ' ' . $state;
                    })
                    ->sortable()
                    ->weight('bold')->color('success'),
                TextColumn::make('status')
                    ->label(__('message.Status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending_bkk_approval' => 'warning',
                        'accepted_bkk'         => 'info',
                        'completed'            => 'success',
                        'Rejected'             => 'danger',
                        'cancelled'            => 'gray',
                        default                => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending_bkk_approval' => __('message.Pending'),
                        'accepted_bkk'         => __('message.Accepted'),
                        'completed'            => __('message.Completed'),
                        'Rejected'             => __('message.Rejected'),
                        'cancelled'            => __('message.Cancelled'),
                        default                => $state,
                    })
                    ->sortable(),

                TextColumn::make('reject_reason')
                    ->label(__('message.Reject Reason'))
                    ->limit(30)->placeholder('—')
                    ->color('danger')
                    ->tooltip(fn ($record) => $record?->reject_reason),

            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->poll('5s')
            ->filters([
                SelectFilter::make('status')
                    ->label(__('message.Status'))
                    ->options([
                        'pending_bkk_approval' => __('message.Pending'),
                        'accepted_bkk'         => __('message.Accepted'),
                        'completed'            => __('message.Completed'),
                        'Rejected'             => __('message.Rejected'),
                        'cancelled'            => __('message.Cancelled'),
                    ]),
            ])

            // ── Row Actions ──────────────────────────────────────────────────
            ->actions([

                // 1. View Invoice (new tab)
                Action::make('view_invoice')
                    ->label(__('message.View Invoice'))
                    ->icon('heroicon-o-document-text')
                    ->color('info')
                    ->tooltip(__('message.View Invoice'))
                    ->url(fn (MoneyTransferInvoice $record): string => $record->invoice_url ?? '#')
                    ->openUrlInNewTab()
                    ->visible(fn (MoneyTransferInvoice $record): bool => !empty($record->invoice_url)),

                // 2. View + Download Transaction Slip
                Action::make('view_slip')
                    ->label(__('message.Transaction Slip'))
                    ->icon('heroicon-o-photo')
                    ->color('success')
                    ->tooltip(__('message.Transaction Slip'))
                    ->modalHeading(__('message.Transaction Slip'))
                    ->modalContent(function (MoneyTransferInvoice $record) {
                        if (empty($record->transaction_slip)) {
                            return view('filament.teller.modals.no-slip', [
                                'message' => __('message.No slip uploaded'),
                            ]);
                        }
                        return view('filament.teller.modals.view-slip', [
                            'slipUrl'       => Storage::url($record->transaction_slip),
                            'invoiceNumber' => $record->invoice_number,
                            'downloadLabel' => __('message.Download Slip'),
                        ]);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel(__('message.Close'))
                    ->visible(fn (MoneyTransferInvoice $record): bool => !empty($record->transaction_slip)),

                // 3. Reject with reason popup
                Action::make('reject')
                    ->label(__('message.Reject'))
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->tooltip(__('message.Reject Transfer Request'))
                    ->modalHeading(__('message.Reject Transfer Request'))
                    ->modalDescription(
                        fn (MoneyTransferInvoice $record) =>
                        __('message.Invoice Number') . ': #' . $record->invoice_number
                    )
                    ->form([
                        Forms\Components\Textarea::make('reject_reason')
                            ->label(__('message.Reason for Rejection'))
                            ->placeholder(__('message.Enter reason for rejection...'))
                            ->required()->minLength(5)->rows(4)->columnSpanFull(),
                    ])
                    ->modalSubmitActionLabel(__('message.Yes, Reject'))
                    ->modalCancelActionLabel(__('message.Cancel'))
                    ->action(function (MoneyTransferInvoice $record, array $data): void {
                        $record->update([
                            'status'        => 'Rejected',
                            'reject_reason' => $data['reject_reason'],
                        ]);
                        Notification::make()
                            ->title(__('message.Rejected Successfully'))
                            ->body(__('message.Invoice') . " {$record->invoice_number} " . __('message.has been rejected.'))
                            ->danger()->send();
                         
                    })
                    ->visible(fn (MoneyTransferInvoice $record): bool =>
                        in_array($record->status, ['pending_bkk_approval', 'accepted_bkk'])
                    ),
            ])

            // ── Header Actions: Export Excel + PDF ───────────────────────────
            ->headerActions([
                // Export to Excel — respects active filters
                ExportAction::make('export_excel')
                    ->label(__('message.Export'))
                    ->icon('heroicon-o-table-cells')
                    ->color('success')
                    ->exports([
                         ExcelExport::make()->fromTable()->except([
                            'Serial_number', 'updated_at',
                        ]),
                    ]),
            ])

            // ── Bulk Actions ─────────────────────────────────────────────────
            ->bulkActions([

                 ExportBulkAction::make('bulk_export_excel')
                    ->label(__('message.Export'))
                    ->color('success')
                    ->exports([
                         ExcelExport::make()->fromTable()->except([
                            'Serial_number', 'updated_at',
                        ]),
                    ]),
            ])

            ->emptyStateIcon('heroicon-o-inbox')
            ->emptyStateHeading(__('message.Today Transfer-OUT'))
            ->emptyStateDescription(__('message.No Transfer-OUT requests today'));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransferOuts::route('/'),
        ];
    }

    public static function canCreate(): bool        { return false; }
    public static function canEdit($record): bool   { return false; }
    public static function canDelete($record): bool { return false; }
}