<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

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

final class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema(self::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->columns(self::getColumns())
            ->filters([
                SelectFilter::make('status')
                    ->multiple()
                    ->options([
                        'active' => 'Aktywne',
                        'finished' => 'Zakończone',
                        'cancelled' => 'Anulowane',
                    ]
                    ),
            ])
            ->actions([
                Tables\Actions\Action::make('offers')
                    ->visible(fn (Order $order): bool => $order->status === 'active')
                    ->color(Color::Fuchsia)
                    ->icon('heroicon-o-users')
                    ->modal()
                    ->form([
                        Forms\Components\Select::make('offer_id')
                            ->options(function ($record) {
                                if (! $record) {
                                    return [];
                                }

                                return $record->offers()
                                    ->with('user')
                                    ->get()
                                    ->mapWithKeys(function ($offer) {
                                        return [
                                            $offer->id => $offer->user->name.' - '.$offer->price.' zł',
                                        ];
                                    });
                            }),
                    ])
                    ->after(function (array $data, $record): void {
                        $record->update(['status' => 'finished']);

                        $offer = $record->offers->where('id', $data['offer_id'])->first();

                        if ($offer) {
                            $offer->update(['status' => 'accepted']);

                            $offer->user->notify(new OrderAccepted());

                            Notification::make()
                                ->title('Sukces!')
                                ->body('Oferta została pomyślnie utworzona.')
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Błąd!')
                                ->body('Nie znaleziono oferty.')
                                ->danger()
                                ->send();
                        }
                    }),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageOrders::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) self::getModel()::count();
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
                        ->placeholder('Enter product name')
                        ->label('Product Name'),
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
                                ->placeholder('Enter category name')
                                ->label('Category Name'),
                        ]),
                ]),
            Forms\Components\TextInput::make('quantity')
                ->minValue(0)
                ->required()
                ->numeric(),
            Forms\Components\TextInput::make('unit')
                ->required(),
            Forms\Components\DatePicker::make('delivery_date')
                ->default(today()),

            Forms\Components\Select::make('status')
                ->default('active')
                ->selectablePlaceholder(false)
                ->options([
                    'active' => 'Aktywne',
                    'finished' => 'Zakończone',
                    'cancelled' => 'Anulowane',
                ]),

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
                ->disk('public')
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
                ->numeric(),
            Tables\Columns\TextColumn::make('unit')
                ->searchable(),
            Tables\Columns\TextColumn::make('delivery_date')
                ->date(),
            Tables\Columns\TextColumn::make('status')
                ->badge()
                ->color(fn ($record): string => match ($record->status) {
                    'active' => 'success',
                    'finished' => 'danger',
                    'cancelled' => 'yellow',
                    default => 'gray',
                }),
            Tables\Columns\TextColumn::make('offers_count')
                ->label('Offers')
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

    public static function getRelations(): array
    {
        return [
            RelationManagers\OffersRelationManager::class,
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('Orders');
    }
}
