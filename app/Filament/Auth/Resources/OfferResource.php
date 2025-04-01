<?php

declare(strict_types=1);

namespace App\Filament\Auth\Resources;

use App\Filament\Auth\Resources\OfferResource\Pages;
use App\Models\Offer;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

final class OfferResource extends Resource
{
    protected static ?string $model = Offer::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Moje oferty';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        if (! auth()->user()->has_access) {
            $table->heading('Twoje Konto Oczekuje na Weryfikację.');
        }

        self::getColumns();

        return $table
            ->striped()
            ->columns([])
            ->filters([
                //
            ])
            ->actions([
                //                Tables\Actions\EditAction::make(),
                //                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageOffers::route('/'),
        ];
    }

    /**
     * @return Tables\Columns\TextColumn[]
     */
    public static function getColumns(): array
    {
        if (auth()->user()->has_access) {
            return [
                Tables\Columns\TextColumn::make('order_id')
                    ->numeric(),
                Tables\Columns\TextColumn::make('price')
                    ->money(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ];
        }

        return [];
    }
}
