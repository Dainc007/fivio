<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\OrderResource\Pages;
use App\Filament\Admin\Resources\OrderResource\RelationManagers;
use App\Models\Address;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
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
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageOrders::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
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
//                    Forms\Components\TextInput::make('price')
//                        ->translateLabel()
//                        ->required()
//                        ->numeric()
//                        ->prefix('$')
//                        ->label('Product Price'),
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
                ->default(today())
            ,

            Forms\Components\Select::make('status')
                ->translateLabel()
                ->default('active')
                ->selectablePlaceholder(false)
                ->options([
                    'active' => 'Aktywne',
                    'finished' => 'Zakończone',
                    'cancelled' => 'Anulowane'
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
                    return (Address::firstOrCreate($data))->id;
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
                ->color(fn(string $state): string => match ($state) {
                    'active' => 'success',
                    'finished' => 'danger',
                    'cancelled' => 'yellow',
                    default => 'gray',
                }),
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
}
