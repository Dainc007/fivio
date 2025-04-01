<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\OrderResource\RelationManagers;

use App\Enums\OfferStatus;
use App\Enums\OrderStatus;
use App\Models\Offer;
use App\Models\Order;
use App\Notifications\OrderAccepted;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

final class OffersRelationManager extends RelationManager
{
    protected static string $relationship = 'offers';

    public function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('price')
            ->columns([
                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($record): string => OfferStatus::from($record->status)->color())
                    ->description(fn($record) => $record->user->name),
                TextColumn::make('price')
                    ->description(fn($record) => $record->comment)->wrap()->lineClamp(3)->tooltip(
                        fn($record) => $record->payment_terms
                    )
                    ->money('PLN'),
                TextColumn::make('delivery_price')->money('PLN'),
                TextColumn::make('quantity')
                    ->tooltip(fn($record) => __('quantityAndOnPalet'))
                    ->description(fn($record) => $record->quantity_on_pallet),
                TextColumn::make('country_origin'),
                TextColumn::make('lote'),
                TextColumn::make('expiry_date')->dateTimeTooltip(),
                TextColumn::make('payment_terms')->wrap()->lineClamp(3)->tooltip(fn($record) => $record->payment_terms),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->multiple()
                    ->options(OfferStatus::withLabels()),
            ])
            ->headerActions([
//                    Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn(Offer $offer): bool => $offer->order->status === OrderStatus::ACTIVE->value)
                    ->color(Color::Fuchsia)
                    ->icon('heroicon-o-users')
                    ->label('Wybierz')
                    ->requiresConfirmation()
                    ->action(function ($record): void {
                        $record->order->update(['status' => OrderStatus::FINISHED->value]);
                        $record->update(['status' => OfferStatus::ACCEPTED->value]);
                        $record->user->notify(new OrderAccepted());

                        $record->order->offers()->where('status', OfferStatus::PENDING->value)->update(
                            ['status' => OfferStatus::REJECTED->value]
                        );

                        Notification::make()
                            ->title('Sukces')
                            ->body('Akcja zakończona sukcesem')
                            ->success()->send();

                    }),
            ], position: Tables\Enums\ActionsPosition::BeforeColumns)
            ->bulkActions([
//                    Tables\Actions\BulkActionGroup::make([
//                        Tables\Actions\DeleteBulkAction::make(),
//                    ]),
            ]);
    }


}
