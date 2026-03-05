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
                Forms\Components\TextInput::make('from_currency')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('to_currency')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('normal_sell_rate')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('normal_buy_rate')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('standard_sell_rate')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('standard_buy_rate')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\DatePicker::make('rate_date')
                    ->required()
                    ->label(__('message.Event date'))
                    ->prefixIcon('heroicon-m-calendar')
                    ->native(false)
                    ->displayFormat('d-m-Y'),
                Forms\Components\TextInput::make('ordering')
                    ->maxLength(255)
                    ->default(null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('from_currency')
                    ->searchable(),
                Tables\Columns\TextColumn::make('to_currency')
                    ->searchable(),
                Tables\Columns\TextColumn::make('normal_sell_rate')
                    ->searchable(),
                Tables\Columns\TextColumn::make('normal_buy_rate')
                    ->searchable(),
                Tables\Columns\TextColumn::make('standard_sell_rate')
                    ->searchable(),
                Tables\Columns\TextColumn::make('standard_buy_rate')
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
