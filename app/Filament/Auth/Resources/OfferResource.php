<?php

declare(strict_types=1);

namespace App\Filament\Auth\Resources;

use App\Enums\Country;
use App\Enums\OfferStatus;
use App\Filament\Auth\Resources\OfferResource\Pages;
use App\Models\Offer;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
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
        if (! auth()->user()->has_access) {
            $table->heading(__('yourAccountIsBeingVerified'));
        }

        return $table
            ->modifyQueryUsing(
                fn (Builder $query) => $query->where('user_id', auth()->id()),
            )
            ->striped()
            ->columns(
                self::getColumns()
            )
            ->filters([
                SelectFilter::make('status')
                    ->multiple()
                    ->options(OfferStatus::withLabels()),

            ])
            ->actions([
                Tables\Actions\EditAction::make()->visible(fn(Offer $record) => $record->status === OfferStatus::PENDING->value),
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
     * @return TextColumn[]
     */
    public static function getColumns(): array
    {
        if (auth()->user()->has_access) {
            return [
                TextColumn::make('order.product.name'),
                TextColumn::make('order_id')
                    ->hidden()
                    ->numeric(),
                TextColumn::make('price')->money(function ($record) {
                    return $record->currency ?? 'pln';
                }),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (Offer $record): string => OfferStatus::from($record->status)->color()),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->toggleable(),
            ];
        }

        return [];
    }

    public static function getNavigationLabel(): string
    {
        return __('My Offers');
    }

    public static function getLabel(): ?string
    {
        return __('My Offers');
    }

    public static function getPluralLabel(): string
    {
        return __('My Offers');
    }

    private static function getFormFields(): array
    {
        return [
            Fieldset::make('Product Information')
                ->schema([
                    TextInput::make('product')
                        ->columnSpan(4)
                        ->default(fn ($record) => $record->product?->name ?? 'No product')
                        ->readOnly()
                        ->disabled(),

                    TextInput::make('quantity')
                        ->suffix('kg')
                        ->columnSpan(2)
                        ->numeric()
                        ->required(),

                    TextInput::make('quantity_on_pallet')
                        ->suffix('kg')
                        ->columnSpan(2)
                        ->numeric(),

                    TextInput::make('lote')
                        ->columnSpan(2),

                    Select::make('country_origin')
                        ->columnSpan(2)
                        ->options(Country::getLabels())
                        ->enum(Country::class)
                        ->searchable(),

                    DatePicker::make('expiry_date')
                        ->columnSpan(2)
                        ->default(today()),
                ])->columns(6),

            Fieldset::make('Pricing & Currency')
                ->schema([
                    Select::make('currency')
                        ->selectablePlaceholder(false)
                        ->options([
                            'pln' => 'ZŁ',
                            'usd' => 'USD',
                            'eur' => 'EUR',
                        ])
                        ->default('pln')
                        ->live()
                        ->columnSpan(2),

                    TextInput::make('price')
                        ->columnSpan(2)
                        ->minValue(0)
                        ->suffix(function (Get $get): string {
                            return match ($get('currency')) {
                                'eur' => '€',
                                'usd' => '$',
                                default => 'zł',
                            };
                        })
                        ->required()
                        ->numeric(),

                    TextInput::make('delivery_price')
                        ->columnSpan(2)
                        ->mask(RawJs::make('$money($input)'))
                        ->stripCharacters(',')
                        ->numeric()
                        ->suffix(function (Get $get): string {
                            return match ($get('currency')) {
                                'eur' => '€',
                                'usd' => '$',
                                default => 'zł',
                            };
                        }),
                ])->columns(6),

            Fieldset::make('deliveryAndPayment')
                ->label(__('deliveryAndPayment'))
                ->schema([
                    DatePicker::make('delivery_date')
                        ->columnSpan(3),

                    Textarea::make('payment_terms')
                        ->columnSpan(3),
                ])->columns(6),

            Fieldset::make('additionalInformation')
                ->label(__('additionalInformation'))
                ->schema([
                    Textarea::make('comment')
                        ->columnSpan(6),

                    FileUpload::make('attachment')
                        ->multiple()
                        ->maxFiles(3)
                        ->columnSpan(6)
                        ->openable()
                        ->downloadable()
                        ->deletable()
                        ->visibility('private')
                        ->directory('attachments')
                        ->disk('public')
                        ->preserveFilenames()
                        ->maxParallelUploads(3),
                ])->columns(6),
        ];
    }
}
