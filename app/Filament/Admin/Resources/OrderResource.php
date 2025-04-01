<?php

namespace App\Filament\Admin\Resources;

use App\Enums\OfferStatus;
use App\Enums\OrderStatus;
use App\Filament\Admin\Resources\OrderResource\Pages;
use App\Filament\Admin\Resources\OrderResource\RelationManagers;
use App\Models\Address;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Notifications\OrderAccepted;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema(self::getForm())->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->columns(self::getColumns())
            ->filters([
                SelectFilter::make('status')
                    ->multiple()
                    ->options(OrderStatus::withLabels()),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                return $query
                    ->with('offers.user')
                    ->withCount('offers');
            });
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\OffersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) self::getModel()::count();
    }

    public static function getNavigationLabel(): string
    {
        return __('Orders');
    }

    public static function getPluralLabel(): string
    {
        return __('Orders');
    }

    public static function getForm(): array
    {
        return [
            Forms\Components\Select::make('product_id')
                ->searchable()
                ->options(Product::all()->pluck('name', 'id'))
                ->relationship('product', 'name')
                ->preload()
                ->required()
                ->createOptionModalHeading('Create New Product')
                ->createOptionForm([
                    Forms\Components\TextInput::make('name')
                        ->unique()
                        ->required()
                        ->placeholder('Enter product name'),
                    Forms\Components\Select::make('category_id')
                        ->searchable()
                        ->options(Category::all()->pluck('name', 'id'))
                        ->relationship('category', 'name')
                        ->preload()
                        ->required()
                        ->createOptionModalHeading('Create New Category')
                        ->createOptionForm([
                            Forms\Components\TextInput::make('name')
                                ->required()
                                ->placeholder('Enter category name'),
                        ]),
                ]),
            Forms\Components\TextInput::make('quantity')
                ->suffix('kg')
                ->minValue(0)
                ->required()
                ->numeric(),

            Forms\Components\DatePicker::make('delivery_date')
                ->default(today()),

            Forms\Components\Select::make('status')
                ->default('active')
                ->selectablePlaceholder(false)
                ->options(OrderStatus::withLabels()),

            Forms\Components\Select::make('address_id')
                ->options(fn () => Address::all()->mapWithKeys(function ($address) {
                    return [
                        $address->id => $address->full_address,
                    ];
                }))
                ->preload()
                ->searchable()
                ->createOptionForm([
                    Forms\Components\TextInput::make('street'),
                    Forms\Components\TextInput::make('street_additional'),
                    Forms\Components\TextInput::make('city'),
                    Forms\Components\TextInput::make('postal_code'),
                    Forms\Components\TextInput::make('country'),
                ])
                ->createOptionUsing(function (array $data): int {
                    return Address::firstOrCreate($data)->id;
                })
                ->createOptionModalHeading('Create New Address'),

            Forms\Components\FileUpload::make('attachment')
                ->multiple()
                ->openable()
                ->downloadable()
                ->deletable()
                ->visibility('private')
                ->directory('attachments')
                ->disk('private')
                ->maxFiles(5)
                ->preserveFilenames()
                ->maxParallelUploads(3),
        ];
    }

    public static function getColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('product.name')
                ->numeric(),
            Tables\Columns\TextColumn::make('quantity')
                ->suffix('kg')
                ->numeric(),
            Tables\Columns\TextColumn::make('delivery_date')
                ->date(),
            Tables\Columns\TextColumn::make('status')
                ->badge()
                ->color(fn (Order $record): string => OrderStatus::from($record->status)->color()),
            Tables\Columns\TextColumn::make('offers_count')
                ->badge()
                ->color('primary'),
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

}
