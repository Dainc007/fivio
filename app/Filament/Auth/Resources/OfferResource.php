<?php

declare(strict_types=1);

namespace App\Filament\Auth\Resources;

use App\Enums\Country;
use App\Filament\Auth\Resources\OfferResource\Pages;
use App\Models\Offer;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

final class OfferResource extends Resource
{
    protected static ?string $model = Offer::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'My Offers';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema(self::getFormFields());
    }

    public static function table(Table $table): Table
    {
        if (!auth()->user()->has_access) {
            $table->heading('Twoje Konto Oczekuje na Weryfikację.');
        }


        return $table
            ->modifyQueryUsing(
                fn(Builder $query) => $query->where('user_id', auth()->id()),
            )
            ->striped()
            ->columns(
                self::getColumns()
            )
            ->filters([
                SelectFilter::make('status')->multiple()
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
//                                Tables\Actions\DeleteAction::make(),
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
                TextColumn::make('order.product.name'),
                TextColumn::make('order_id')
                    ->hidden()
                    ->numeric(),
                TextColumn::make('price')
                    ->money(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ];
        }

        return [];
    }

    public static function getNavigationLabel(): string
    {
        return __('My Offers');
    }

    private static function getFormFields(): array
    {
        return [
            TextInput::make('product')
                ->columnSpan(4)
                ->default(fn($record) => $record->product?->name ?? 'No product')
                ->readOnly()
                ->disabled(),

            TextInput::make('quantity')
                ->suffix('kg')
                ->columnSpan(2)
                ->numeric()
                ->suffix('kg')
                ->required(),

            TextInput::make('quantity_on_pallet')
                ->suffix('kg')
                ->columnSpan(2) // Kolumna 1
                ->numeric()
                ->suffix('kg')
                ->required(),

            TextInput::make('price')
                ->columnSpan(2)
                ->minValue(0)
                ->required()
                ->numeric()
                ->suffix('zł'),

            TextInput::make('delivery_price')
                ->columnSpan(2)
                ->minValue(0)
                ->numeric()
                ->suffix('zł'),


            TextInput::make('lote')
                ->columnSpan(1),

            Select::make('country_origin')
                ->columnSpan(1)
                ->options(Country::getLabels())
                ->enum(Country::class)
                ->searchable(),

            DatePicker::make('expiry_date')
                ->columnSpan(2)
                ->default(today()),

            Textarea::make('payment_terms')
                ->columnSpan(2),
            Textarea::make('comment')
                ->columnSpan(2),

            FileUpload::make('attachment')
                ->multiple()
                ->maxFiles(3)
                ->columnSpan(4)
                ->openable()
                ->downloadable()
                ->deletable()
                ->visibility('private')
                ->directory('attachments')
                ->disk('public')
                ->preserveFilenames()
                ->maxParallelUploads(3),
        ];
    }
}
