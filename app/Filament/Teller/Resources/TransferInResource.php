<?php

namespace App\Filament\Teller\Resources;

use App\Filament\Teller\Resources\TransferInResource\Pages;
use App\Models\MoneyTransferInvoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Str;

class TransferInResource extends Resource
{
    protected static ?string $model = MoneyTransferInvoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-left';

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('message.Transfer-IN Requests');
    }

    public static function getModelLabel(): string
    {
        return __('message.Transfer-IN Request');
    }

    public static function getPluralModelLabel(): string
    {
        return __('message.Transfer-IN Requests');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('message.Transfers');
    }

    public static function getNavigationBadge(): ?string
    {
        $count = MoneyTransferInvoice::query()
            ->where('transfer_type', 'Transfer-IN')
            ->whereDate('created_at', Carbon::today())
            ->whereIn('status', ['pending_bkk_approval', 'accepted_bkk'])
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return __('message.Pending') . ' ' . __('message.Transfer-IN');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('transfer_type', 'Transfer-IN')
            ->whereDate('created_at', Carbon::today())
            ->latest();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll('8s')
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('Serial_number')
                    ->label(__('message.Serial number'))
                    ->badge()
                    ->state(fn($column) => $column->getRowLoop()->iteration),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('message.Time'))
                    ->dateTime('d M Y h:i')
                    ->searchable()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label(__('message.Invoice Number'))
                    ->searchable()->sortable()->copyable()
                    ->weight('bold')->color('primary'),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('message.Status'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pending_bkk_approval' => __('message.Pending'),
                        'accepted_bkk'         => __('message.Accepted'),
                        'completed'            => __('message.Completed'),
                        'Rejected'             => __('message.Rejected'),
                        'cancelled'            => __('message.Cancelled'),
                        default                => $state,
                    })
                    ->color(fn ($state) => match ($state) {
                        'pending_bkk_approval' => 'warning',
                        'accepted_bkk'         => 'info',
                        'completed'            => 'success',
                        'Rejected'             => 'danger',
                        'cancelled'            => 'gray',
                        default                => 'gray',
                    })
                    ->icon(fn ($state) => match ($state) {
                        'pending_bkk_approval' => 'heroicon-o-clock',
                        'accepted_bkk'         => 'heroicon-o-check-circle',
                        'completed'            => 'heroicon-o-check-badge',
                        'Rejected'             => 'heroicon-o-x-circle',
                        'cancelled'            => 'heroicon-o-ban',
                        default                => null,
                    }),

                Tables\Columns\TextColumn::make('combinessd')
                    ->label(__('message.Customer name'))
                    ->html()
                    ->getStateUsing(fn ($record) =>
                        '<strong>' . Str::ucfirst($record->customer_name) . '</strong><br>' .
                        Str::ucfirst($record->phone)
                    ),
                Tables\Columns\TextColumn::make('bank_details')
                    ->label(__('message.Bank Details'))
                    ->html()
                    ->getStateUsing(fn ($record) =>
                        '<strong>' . Str::ucfirst($record->bank_name) . '</strong><br>' .
                        Str::ucfirst($record->acc_number) . '<br>' .
                        Str::ucfirst($record->acc_name)
                    )
                    ->copyable(),
                Tables\Columns\TextColumn::make('entered_amount')
                    ->label(__('message.Amount'))
                    ->formatStateUsing(function ($state, $record) {
                        return $record->currency . ' ' . $state;
                    })
                    ->searchable()
                    ->alignRight(),
                Tables\Columns\TextColumn::make('trf_fee')
                    ->label(__('message.Transfer Fee'))
                    ->formatStateUsing(function ($state, $record) {
                        return $record->currency . ' ' . $state;
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('net_amount')
                    ->label(__('message.Net Amount'))
                    ->formatStateUsing(function ($state, $record) {
                        return $record->currency . ' ' . $state;
                    })
                    ->searchable()
                    ->weight('bold')->color('success'),
                Tables\Columns\TextColumn::make('reject_reason')
                    ->label(__('message.Reject Reason'))
                    ->limit(30)->placeholder('—')
                    ->color('danger')
                    ->searchable()
                    ->tooltip(fn ($record) => $record?->reject_reason),
            ])
            ->actions([
                // 1. View Invoice (new tab)
                Tables\Actions\Action::make('view_invoice')
                    ->label(__('message.View Invoice'))
                    ->icon('heroicon-o-document-text')
                    ->color('info')
                    ->tooltip(__('message.View Invoice'))
                    ->url(fn (MoneyTransferInvoice $record): string => $record->invoice_url ?? '#')
                    ->openUrlInNewTab()
                    ->visible(fn (MoneyTransferInvoice $record): bool => !empty($record->invoice_url)),

                // View Transaction Slip
                // Tables\Actions\Action::make('view_slip')
                //     ->label(__('message.View_Slip'))
                //     ->icon('heroicon-o-photo')
                //     ->color('info')
                //     ->modalHeading(__('message.Transaction_Slip'))
                //     ->modalWidth('lg')
                //     ->modalSubmitActionLabel(__('message.Download'))
                //     ->form([
                //         Forms\Components\ViewField::make('slip_preview')
                //             ->view('filament.teller.components.slip-preview')
                //             ->viewData(fn ($record) => ['slip_url' => $record->transaction_slip ? Storage::url($record->transaction_slip) : null]),
                //     ])
                //     ->action(function ($record) {
                //         // Download handled via JS in view
                //     })
                //     ->visible(fn ($record) => !empty($record->transaction_slip)),
                Tables\Actions\Action::make('view_slip')
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

                // Reject Action
                Tables\Actions\Action::make('reject')
                    ->label(__('message.Reject'))
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation(false)
                    ->modalHeading(__('message.Reject_Transfer-IN_Request'))
                    ->modalWidth('md')
                    ->modalSubmitActionLabel(__('message.Reject'))
                    ->modalSubmitAction(fn ($action) => $action->color('danger')->icon('heroicon-o-x-circle'))
                    ->form([
                        Forms\Components\Select::make('reject_reason')
                            ->label(__('message.Reason_for_Rejection'))
                            ->required()
                            ->options([
                                'Wrong Account details' => __('message.Wrong_Account_details'),
                                'Wrong amount'          => __('message.Wrong_amount'),
                                'Change mind'           => __('message.Change_mind'),
                                'Other'                 => __('message.Other'),
                            ])
                            ->live()
                            ->placeholder(__('message.Select_an_option')),

                        Forms\Components\Textarea::make('other_reason')
                            ->label(__('message.Specify_Reason'))
                            ->placeholder(__('message.Enter_rejection_reason'))
                            ->rows(3)
                            ->visible(fn (Forms\Get $get) => $get('reject_reason') === 'Other')
                            ->requiredIf('reject_reason', 'Other'),
                    ])
                    ->action(function ($record, array $data) {
                        $reason = $data['reject_reason'] === 'Other'
                            ? ($data['other_reason'] ?? 'Other')
                            : $data['reject_reason'];

                        $record->update([
                            'status'        => 'Rejected',
                            'reject_reason' => $reason,
                        ]);

                        Notification::make()
                            ->title(__('message.Transfer-IN') . ' ' . __('message.Rejected'))
                            ->body(__('message.Invoice') . ' #' . $record->invoice_number . ' ' . __('message.has_been_rejected'))
                            ->danger()
                            ->send();
                    })
                    ->visible(fn ($record) => in_array($record->status, ['pending_bkk_approval', 'accepted_bkk'])),
            ])
            ->bulkActions([])
            ->emptyStateHeading(__('message.No_Transfer-IN_today'))
            ->emptyStateDescription(__('message.No_Transfer-IN_records_found_for_today'))
            ->emptyStateIcon('heroicon-o-inbox');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransferIns::route('/'),
        ];
    }
}