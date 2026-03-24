<?php

namespace App\Filament\Bkkoffice\Resources;

use App\Filament\Bkkoffice\Resources\TransferInvoiceResource\Pages;
use App\Models\MoneyTransferInvoice;
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

class TransferInvoiceResource extends Resource
{
    protected static ?string $model = MoneyTransferInvoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-right';

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('message.Transfer-OUT Requests');
    }

    public static function getModelLabel(): string
    {
        return __('message.Transfer-OUT Requests');
    }

    public static function getPluralModelLabel(): string
    {
        return __('message.Transfer-OUT Requests');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('message.Transfers');
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('status', 'pending_bkk_approval')
            ->where('transfer_type', 'Transfer-OUT')
            ->whereDate('created_at', today())
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return __('message.Pending Transfer-OUT approvals');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make(__('message.Transfer Details'))
                ->schema([
                    Forms\Components\TextInput::make('invoice_number')
                        ->label(__('message.Invoice Number'))->disabled(),
                    Forms\Components\TextInput::make('customer_name')
                        ->label(__('message.Customer Name'))->disabled(),
                    Forms\Components\TextInput::make('phone')
                        ->label(__('message.Phone'))->disabled(),
                    Forms\Components\TextInput::make('bank_name')
                        ->label(__('message.Bank Name'))->disabled(),
                    Forms\Components\TextInput::make('acc_name')
                        ->label(__('message.Account Name'))->disabled(),
                    Forms\Components\TextInput::make('acc_number')
                        ->label(__('message.Account Number'))->disabled(),
                    Forms\Components\TextInput::make('currency')
                        ->label(__('message.Currency'))->disabled(),
                    Forms\Components\TextInput::make('entered_amount')
                        ->label(__('message.Amount'))->disabled(),
                    Forms\Components\TextInput::make('trf_fee')
                        ->label(__('message.Transfer Fee'))->disabled(),
                    Forms\Components\TextInput::make('net_amount')
                        ->label(__('message.Net Amount'))->disabled(),
                    Forms\Components\Select::make('status')
                        ->label(__('message.Status'))
                        ->options([
                            'pending_bkk_approval' => __('message.Pending BKK Approval'),
                            'accepted_bkk'         => __('message.Accepted'),
                            'completed'            => __('message.Completed'),
                            'Rejected'             => __('message.Rejected'),
                            'cancelled'            => __('message.Cancelled'),
                        ]),
                    Forms\Components\Textarea::make('reject_reason')
                        ->label(__('message.Reason for Rejection'))
                        ->disabled()
                        ->rows(2)
                        ->columnSpanFull()
                        ->visible(fn ($record) => $record?->status === 'Rejected'),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                MoneyTransferInvoice::query()
                    ->where('transfer_type', 'Transfer-OUT')
                    ->whereDate('created_at', today())
                    ->orderBy('id', 'desc')
            )
            ->columns([
                 TextColumn::make('Serial_number')
                ->label(__('message.Serial number'))
                ->badge()
                ->state(fn($column) => $column->getRowLoop()->iteration),
                TextColumn::make('created_at')
                    ->label(__('message.Time'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->color('gray'),

                TextColumn::make('invoice_number')
                    ->label(__('message.Invoice #'))
                    ->searchable()
                    ->copyable()
                    ->weight('bold')
                    ->color('primary'),

                TextColumn::make('customer_name')
                    ->label(__('message.Customer'))
                    ->searchable()
                    ->default('—'),

                TextColumn::make('phone')
                    ->label(__('message.Phone'))
                    ->default('—'),

                TextColumn::make('bank_name')
                    ->label(__('message.Bank Name'))
                    ->badge()
                    ->color('info'),

                TextColumn::make('acc_number')
                    ->label(__('message.Account Number'))
                    ->copyable(),

                TextColumn::make('currency')
                    ->label(__('message.Currency'))
                    ->badge(),

                TextColumn::make('entered_amount')
                    ->label(__('message.Amount'))
                    ->numeric(thousandsSeparator: ',')
                    ->color('warning')
                    ->weight('bold'),

                TextColumn::make('trf_fee')
                    ->label(__('message.Fee'))
                    ->numeric(thousandsSeparator: ',')
                    ->color('gray'),

                TextColumn::make('net_amount')
                    ->label(__('message.Net Amount'))
                    ->numeric(thousandsSeparator: ',')
                    ->color('success')
                    ->weight('bold'),

                TextColumn::make('transaction_slip')
                    ->label(__('message.Slip'))
                    ->formatStateUsing(fn ($state) => $state
                        ? '✅ ' . __('message.Uploaded')
                        : '—')
                    ->color(fn ($state) => $state ? 'success' : 'gray'),

                Tables\Columns\BadgeColumn::make('status')
                    ->label(__('message.Status'))
                    ->colors([
                        'warning' => 'pending_bkk_approval',
                        'success' => 'accepted_bkk',
                        'primary' => 'completed',
                        'danger'  => 'Rejected',
                        'gray'    => 'cancelled',
                    ])
                    ->formatStateUsing(fn ($state) => match($state) {
                        'pending_bkk_approval' => '⏳ ' . __('message.Pending'),
                        'accepted_bkk'         => '✅ ' . __('message.Accepted'),
                        'completed'            => '✔ '  . __('message.Completed'),
                        'Rejected'             => '❌ ' . __('message.Rejected'),
                        'cancelled'            => '🚫 ' . __('message.Cancelled'),
                        default                => $state,
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('message.Status'))
                    ->options([
                        'pending_bkk_approval' => '⏳ ' . __('message.Pending'),
                        'accepted_bkk'         => '✅ ' . __('message.Accepted'),
                        'completed'            => '✔ '  . __('message.Completed'),
                        'Rejected'             => '❌ ' . __('message.Rejected'),
                    ])
                    ->placeholder(__('message.All Statuses')),
            ])
            ->actions([
                Action::make('view_invoice')
                    ->label(__('message.View Invoice'))
                    ->icon('heroicon-o-document-text')
                    ->color('info')
                    ->url(fn (MoneyTransferInvoice $record) => $record->invoice_url)
                    ->openUrlInNewTab()
                    ->visible(fn (MoneyTransferInvoice $record) => ! empty($record->invoice_url)),

                Action::make('accept')
                    ->label(__('message.Accept'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading(__('message.Accept Transfer Request'))
                    ->modalDescription(__('message.Are you sure you want to accept this Transfer-OUT request? This will notify the teller at Poipet.'))
                    ->modalSubmitActionLabel(__('message.Yes, Accept'))
                    ->modalCancelActionLabel(__('message.Cancel'))
                    ->action(function (MoneyTransferInvoice $record) {
                        $record->update(['status' => 'accepted_bkk']);

                        Notification::make()
                            ->title(__('message.Transfer Accepted'))
                            ->body(__('message.Invoice') . " {$record->invoice_number} " . __('message.has been accepted.'))
                            ->success()
                            ->send();
                    })
                    ->visible(fn (MoneyTransferInvoice $record) => $record->status === 'pending_bkk_approval'),

                // ✅ FIXED: reject_reason now saved to DB
                Action::make('reject')
                    ->label(__('message.Reject'))
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->modalHeading(__('message.Reject Transfer Request'))
                    ->modalSubmitActionLabel(__('message.Yes, Reject'))
                    ->modalCancelActionLabel(__('message.Cancel'))
                    ->form([
                        Forms\Components\Textarea::make('reject_reason')
                            ->label(__('message.Reason for Rejection'))
                            ->placeholder(__('message.Enter reason for rejection...'))
                            ->required()
                            ->minLength(3)
                            ->rows(4),
                    ])
                    ->action(function (MoneyTransferInvoice $record, array $data) {
                        // ✅ Save both status AND reject_reason
                        $record->update([
                            'status'        => 'Rejected',
                            'reject_reason' => $data['reject_reason'],
                        ]);

                        Notification::make()
                            ->title(__('message.Transfer Rejected'))
                            ->body(__('message.Invoice') . " {$record->invoice_number} " . __('message.has been rejected.'))
                            ->warning()
                            ->send();
                    })
                    ->visible(fn (MoneyTransferInvoice $record) => $record->status === 'pending_bkk_approval'),

                Action::make('upload_slip')
                    ->label(__('message.Upload Slip'))
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('primary')
                    ->modalHeading(__('message.Upload Transaction Slip'))
                    ->modalDescription(__('message.Upload the bank transfer receipt after processing the payment.'))
                    ->modalSubmitActionLabel(__('message.Upload & Complete'))
                    ->modalCancelActionLabel(__('message.Cancel'))
                    ->modalWidth('md')
                    ->form([
                        Forms\Components\Placeholder::make('invoice_info')
                            ->label(__('message.Invoice'))
                            ->content(fn (MoneyTransferInvoice $record) =>
                                "{$record->invoice_number} — {$record->bank_name} | {$record->acc_number} | {$record->currency} " . number_format($record->net_amount, 2)
                            ),
                        Forms\Components\FileUpload::make('transaction_slip')
                            ->label(__('message.Transaction Slip'))
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->directory('transaction-slips')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'])
                            ->maxSize(5120)
                            ->required()
                            ->helperText(__('message.Drag & drop or click to upload. Max 5MB.'))
                            ->placeholder(__('message.Drop slip here or click to browse')),
                    ])
                    ->action(function (MoneyTransferInvoice $record, array $data) {
                        $record->update([
                            'transaction_slip' => $data['transaction_slip'],
                            'status'           => 'completed',
                        ]);

                        Notification::make()
                            ->title('✅ ' . __('message.Completed!'))
                            ->body(__('message.Invoice') . " {$record->invoice_number} " . __('message.marked as completed. Slip saved.'))
                            ->success()
                            ->send();
                    })
                    ->visible(fn (MoneyTransferInvoice $record) => $record->status === 'accepted_bkk'),

                Action::make('view_slip')
                    ->label(__('message.View Slip'))
                    ->icon('heroicon-o-photo')
                    ->color('gray')
                    ->url(fn (MoneyTransferInvoice $record) => asset('storage/' . $record->transaction_slip))
                    ->openUrlInNewTab()
                    ->visible(fn (MoneyTransferInvoice $record) =>
                        $record->status === 'completed' && ! empty($record->transaction_slip)
                    ),
            ])
            ->bulkActions([])
            ->poll('8s')
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransferInvoices::route('/'),
        ];
    }
}