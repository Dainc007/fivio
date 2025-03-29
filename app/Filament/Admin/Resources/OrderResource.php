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

    protected static ?string $label = 'Zapotrzebowanie';

    protected static ?string $pluralLabel = 'Zapotrzebowanie';

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
                    ->translateLabel()
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
                            ->translateLabel()
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
                        ->translateLabel()
                        ->unique()
                        ->required()
                        ->placeholder('Enter product name')
                        ->label('Product Name'),
                    Forms\Components\Select::make('category_id')
                        ->translateLabel()
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
                ->translateLabel()
                ->minValue(0)
                ->required()
                ->numeric(),
            Forms\Components\TextInput::make('unit')
                ->translateLabel()
                ->required(),
            Forms\Components\DatePicker::make('delivery_date')
                ->translateLabel()
                ->default(today()),

            Forms\Components\Select::make('status')
                ->translateLabel()
                ->default('active')
                ->selectablePlaceholder(false)
                ->options([
                    'active' => 'Aktywne',
                    'finished' => 'Zakończone',
                    'cancelled' => 'Anulowane',
                ]),

            Forms\Components\Select::make('address_id')
                ->translateLabel()
                ->options(fn () => Address::all()->mapWithKeys(function ($address) {
                    return [
                        $address->id => $address->full_address,
                    ];
                }))
                ->preload()
                ->searchable()
                ->createOptionForm([
                    Forms\Components\TextInput::make('street')
                        ->translateLabel(),
                    Forms\Components\TextInput::make('street_additional')
                        ->translateLabel(),
                    Forms\Components\TextInput::make('city')
                        ->translateLabel(),
                    Forms\Components\TextInput::make('postal_code')
                        ->translateLabel(),
                    Forms\Components\TextInput::make('country')
                        ->translateLabel(),
                ])
                ->createOptionUsing(function (array $data): int {
                    return Address::firstOrCreate($data)->id;
                })
                ->createOptionModalHeading('Create New Address'),

            Forms\Components\FileUpload::make('attachment')
                ->translateLabel()
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
                ->alignCenter()
                ->translateLabel()
                ->numeric()
                ->toggleable()
                ->sortable(),
            Tables\Columns\TextColumn::make('quantity')
                ->alignCenter()
                ->translateLabel()
                ->numeric()
                ->toggleable()
                ->sortable(),
            Tables\Columns\TextColumn::make('unit')
                ->alignCenter()
                ->translateLabel()
                ->toggleable()
                ->searchable(),
            Tables\Columns\TextColumn::make('delivery_date')
                ->alignCenter()
                ->translateLabel()
                ->date()
                ->toggleable()
                ->sortable(),
            Tables\Columns\TextColumn::make('status')
                ->translateLabel()
                ->alignCenter()
                ->badge()
                ->toggleable()
                ->color(fn ($record): string => match ($record->status) {
                    'active' => 'success',
                    'finished' => 'danger',
                    'cancelled' => 'yellow',
                    default => 'gray',
                }),
            Tables\Columns\TextColumn::make('offers_count')
                ->label('Offers')
                ->badge()
                ->color('primary')
                ->sortable(),
            Tables\Columns\TextColumn::make('address.full_address')
                ->translateLabel()
                ->alignCenter()
                ->limit(50)
                ->wrap()
                ->toggleable(),
            Tables\Columns\TextColumn::make('created_at')
                ->alignCenter()
                ->translateLabel()
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('updated_at')
                ->alignCenter()
                ->translateLabel()
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\OffersRelationManager::class,
        ];
    }
}
