<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExchangeRateResource\Pages;
use App\Filament\Resources\ExchangeRateResource\RelationManagers;
use App\Models\ExchangeRate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Forms\Components\DatePicker;

class ExchangeRateResource extends Resource
{
    protected static ?string $model = ExchangeRate::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Forms\Components\Select::make('section')
                //     ->label(__('message.section'))
                //     ->options([
                //                 'left' => 'Left section',
                //                 'right' => 'right section',
                //             ])
                //             ->prefixIcon('heroicon-m-document-currency-dollar')
                //     ->required(),
                Forms\Components\Select::make('buy_or_sell')
                    ->label(__('message.Buy/Sell'))
                    ->options([
                                'sell' => 'Sell',
                                'buy' => 'Buy',
                            ])
                    ->prefixIcon('heroicon-m-document-currency-dollar')
                    ->columnSpan('full')
                    ->required(),
                Forms\Components\Select::make('from_currency')
                     ->options([
                                'Dollar' => 'Dollar',
                                'Baht' => 'Baht',
                                'Riel' => 'Riel',
                            ])
                    ->prefixIcon('heroicon-m-document-currency-dollar')
                    ->required(),
                Forms\Components\Select::make('to_currency')
                     ->options([
                                'Dollar' => 'Dollar',
                                'Baht' => 'Baht',
                                'Riel' => 'Riel',
                            ])
                    ->prefixIcon('heroicon-m-document-currency-dollar')
                    ->required(),
                Forms\Components\TextInput::make('normal_rate')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('standard_rate')
                    ->maxLength(255)
                    ->default(null),
               
              
                Forms\Components\DatePicker::make('rate_date')
                    ->required()
                    ->label(__('message.Event date'))
                    ->prefixIcon('heroicon-m-calendar')
                    ->native(false)
                    ->displayFormat('d-m-Y'),
                // Forms\Components\TextInput::make('ordering')
                //     ->maxLength(255)
                //     ->default(null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('Serial_number')
                    ->label(__('message.Serial number'))
                    ->badge()
                    ->state(fn($column) => $column->getRowLoop()->iteration),
                //  Tables\Columns\TextColumn::make('section')
                //     ->searchable(),
                Tables\Columns\TextColumn::make('buy_or_sell')
                    ->searchable(),
                Tables\Columns\TextColumn::make('from_currency')
                    ->searchable(),
                Tables\Columns\TextColumn::make('to_currency')
                    ->searchable(),
               
                
                Tables\Columns\TextColumn::make('normal_rate')
                    ->searchable(),
                Tables\Columns\TextColumn::make('standard_rate')
                    ->searchable(),
                Tables\Columns\TextColumn::make('rate_date')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ordering')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('ordering', 'asc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExchangeRates::route('/'),
            // 'create' => Pages\CreateExchangeRate::route('/create'),
            // 'view' => Pages\ViewExchangeRate::route('/{record}'),
            // 'edit' => Pages\EditExchangeRate::route('/{record}/edit'),
        ];
    }
}
