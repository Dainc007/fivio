<?php

declare(strict_types=1);

namespace App\Filament\Auth\Resources;

use App\Enums\Country;
use App\Enums\OrderStatus;
use App\Filament\Auth\Resources\OrderResource\Pages;
use App\Models\Offer;
use App\Models\Order;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

final class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $label = 'Order';

    protected static ?string $pluralLabel = 'Orders';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema(self::getFormFields());
    }

    public static function table(Table $table): Table
    {
        $columns = self::getColumns();

        if (! auth()->user()->has_access) {
            $table->heading(__('yourAccountIsBeingVerified'));
        }

        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->withExists('userOffers'))
            ->striped()
            ->columns($columns)
            ->filters([
                SelectFilter::make('status')
                    ->multiple()
                    ->options(OrderStatus::withLabels()),
            ])
            ->actions([
                Tables\Actions\Action::make('offer')
                    ->label(__('makeOffer'))
                    ->visible(function ($record): bool {
                        return auth()->user()->has_access && ! $record->userHasSubmittedOffer && $record->status === OrderStatus::ACTIVE->value;
                    })
                    ->icon('heroicon-o-plus')
                    ->color(Color::Fuchsia)
                    ->modal()
                    ->form(self::getFormFields())
                    ->after(function (array $data, $record): void {
                        $data['order_id'] = $record->id;
                        $data['user_id'] = auth()->user()->id;
                        Offer::updateOrCreate([
                            'order_id' => $data['order_id'],
                            'user_id' => $data['user_id'],
                        ],
                            $data
                        );

                        Notification::make()
                            ->title(__('success'))
                            ->body(__('actionSuccessful'))
                            ->success()->send();
                    }),
                Tables\Actions\Action::make('offerMade')
                    ->label(__('offerMade'))
                    ->icon('heroicon-o-check-circle')
                    ->color(Color::Green)
                    ->visible(function ($record) {
                        return $record->userHasSubmittedOffer;
                    })
                    ->disabled(),
                Tables\Actions\Action::make('editOffer')
                    ->label(__('editOffer'))
                    ->icon('heroicon-o-pencil')
                    ->color(Color::Blue)
                    ->visible(function ($record) {
                        return $record->userHasSubmittedOffer;
                    })
                    ->modal()
                    ->form(self::getFormFields())
                    ->fillForm(function ($record) {
                        $offer = Offer::where('order_id', $record->id)
                            ->where('user_id', auth()->user()->id)
                            ->firstOrFail();
                        $offer->product = $record->product->name;

                        return $offer->toArray();
                    })
                    ->after(function (array $data, $record): void {
                        $offer = Offer::where('order_id', $record->id)
                            ->where('user_id', auth()->user()->id)
                            ->firstOrFail();

                        if ($offer) {
                            $offer->fill($data);
                            $offer->save();
                        }

                        Notification::make()
                            ->title(__('success'))
                            ->body(__('actionSuccessful'))
                            ->success()->send();
                    }),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageOrders::route('/'),
        ];
    }

    public static function getColumns(): array
    {
        $columns = [];

        if (auth()->user()->has_access) {
            return [
                TextColumn::make('order_id')
                    ->hidden(),
                TextColumn::make('product.name'),
                TextColumn::make('quantity')
                    ->suffix('kg')
                    ->numeric(),
                TextColumn::make('delivery_date')
                    ->date(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (Order $record): string => OrderStatus::from($record->status)->color()),
                TextColumn::make('address.full_address')
                    ->limit(50)
                    ->wrap(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ];
        }

        return $columns;
    }

    public static function getNavigationLabel(): string
    {
        return __('Orders');
    }

    public static function getLabel(): ?string
    {
        return __('Orders');
    }

    public static function getPluralLabel(): string
    {
        return __('Orders');
    }

    private static function getFormFields(): array
    {
        return [
            Fieldset::make('productInformation')
                ->label(__('productInformation'))
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

            Fieldset::make('pricingAndCurrency')
                ->label(__('pricingAndCurrency'))
                ->schema([
                    Select::make('currency')
                        ->selectablePlaceholder(false)
                        ->options([
                            'pln' => 'ZŁ',
                            'usd' => 'USD',
                            'eur' => 'EUR',
                        ])
                        ->live()
                        ->columnSpan(1),

                    TextInput::make('price')
                        ->mask(RawJs::make('$money($input)'))
                        ->stripCharacters(',')
                        ->numeric()

                        ->columnSpan(3)
                        ->suffix(function (Get $get): string {
                            return match ($get('currency')) {
                                'eur' => '€',
                                'usd' => '$',
                                default => 'zł',
                            };
                        }),

                    TextInput::make('delivery_price')
                        ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('deliveryPriceTooltip'))
                        ->mask(RawJs::make('$money($input)'))
                        ->stripCharacters(',')
                        ->numeric()
                        ->columnSpan(2)
                        ->suffix(function (Get $get): string {
                            return match ($get('currency')) {
                                'eur' => '€',
                                'usd' => '$',
                                default => 'zł',
                            };
                        }),
                ])->columns(6),

            Fieldset::make('DeliveryAndPayment')
                ->label(__('deliveryAndPayment'))
                ->schema([
                    DatePicker::make('delivery_date')
                        ->columnSpan(5),

                    Textarea::make('payment_terms')
                        ->columnSpan(6),
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
