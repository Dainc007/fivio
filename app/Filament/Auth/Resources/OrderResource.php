<?php

declare(strict_types=1);

namespace App\Filament\Auth\Resources;

use App\Enums\Country;
use App\Enums\OfferStatus;
use App\Enums\OrderStatus;
use App\Filament\Auth\Resources\OrderResource\Pages;
use App\Models\Offer;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

final class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $label = 'Zapotrzebowanie';

    protected static ?string $pluralLabel = 'Zapotrzebowanie';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema(self::getFormFields());
    }

    public static function table(Table $table): Table
    {
        $columns = self::getColumns();

        if (!auth()->user()->has_access) {
            $table->heading('Twoje Konto Oczekuje na Weryfikację.');
        }

        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->withExists('userOffers'))
            ->striped()
            ->columns($columns)
            ->filters([
                SelectFilter::make('status')
                    ->multiple()
                    ->options(OrderStatus::withLabels()),
            ])
            ->actions([
                Tables\Actions\Action::make('offer')
                    ->visible(function ($record): bool {
                        return !$record->userHasSubmittedOffer && $record->status === OrderStatus::ACTIVE->value;
                    })
                    ->icon('heroicon-o-plus')
                    ->color(Color::Fuchsia)
                    ->modal()
                    ->form(self::getFormFields())
                    ->after(function (array $data, $record): void {
                        Offer::create($data);

                        Notification::make()->success()->send();
                    }),
                Tables\Actions\Action::make('offerMade')
                    ->icon('heroicon-o-check-circle')
                    ->color(Color::Green)
                    ->visible(function ($record) {
                        return $record->userHasSubmittedOffer;
                    })
                    ->disabled(),
                Tables\Actions\Action::make('editOffer')
                    ->icon('heroicon-o-pencil')
                    ->color(Color::Blue)
                    ->visible(function ($record) {
                        return $record->userHasSubmittedOffer;
                    })
                    ->modal()
                    ->form(self::getFormFields())
                    ->after(function (array $data, $record): void {
                        $record->offer?->update(['price' => $data['price']]);

                        Notification::make()->success()->send();
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

    /**
     * @return Tables\Columns\TextColumn[]
     */
    public static function getColumns(): array
    {
        $columns = [];

        if (auth()->user()->has_access) {
            return [
                Tables\Columns\TextColumn::make('order_id')
                    ->hidden(),
                Tables\Columns\TextColumn::make('product.name')
                    ->numeric(),
                Tables\Columns\TextColumn::make('quantity')
                    ->suffix('kg')
                    ->numeric(),
                Tables\Columns\TextColumn::make('delivery_date')
                    ->date(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(Order $record): string => OrderStatus::from($record->status)->color()),
                Tables\Columns\TextColumn::make('address.full_address')
                    ->limit(50)
                    ->wrap(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
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

    private static function getFormFields(): array
    {
        return [
            Forms\Components\TextInput::make('product')
                ->columnSpan(4)
                ->default(fn($record) => $record->product?->name ?? 'No product')
                ->readOnly(),

            Forms\Components\TextInput::make('quantity')
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
                ->suffix('zł')
                ->default(fn($record) => auth()->user()->offers->where('order_id', $record->id)->pluck('price')),

            TextInput::make('delivery_price')
                ->columnSpan(2)
                ->minValue(0)
                ->numeric()
                ->suffix('zł'),


            TextInput::make('lote')
                ->columnSpan(1),

            Forms\Components\Select::make('country_origin')
                ->columnSpan(1)
                ->options(Country::getLabels())
                ->enum(Country::class)
                ->searchable(),

            DatePicker::make('expiry_date')
                ->columnSpan(2)
                ->default(today()),

            Forms\Components\Textarea::make('payment_terms')
                ->columnSpan(4),

            Forms\Components\FileUpload::make('attachment')
                ->columnSpan(4)
                ->openable()
                ->downloadable()
                ->deletable()
                ->visibility('private')
                ->directory('attachments')
                ->disk('private')
                ->preserveFilenames()
                ->maxParallelUploads(3),

            TextInput::make('order_id')->hidden()->default(fn($record) => $record->id),
            TextInput::make('user_id')->hidden()->default(auth()->id()),
        ];
    }


}

