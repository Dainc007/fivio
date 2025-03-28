<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\OrderResource\Pages;
use App\Filament\Admin\Resources\OrderResource\RelationManagers;
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
                ->translateLabel()
                ->searchable()
                ->options(Product::all()->pluck('name', 'id'))
                ->relationship('product', 'name')
                ->required()
                ->createOptionModalHeading('Create New Product')
                ->createOptionForm([
                    Forms\Components\TextInput::make('name')
                        ->translateLabel()
                        ->unique()
                        ->required()
                        ->placeholder('Enter product name')
                        ->label('Product Name'),
                    Forms\Components\TextInput::make('price')
                        ->translateLabel()
                        ->required()
                        ->numeric()
                        ->prefix('$')
                        ->label('Product Price'),
                    Forms\Components\Select::make('category_id')
                        ->translateLabel()
                        ->searchable()
                        ->options(Category::all()->pluck('name', 'id'))
                        ->relationship('category', 'name')
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
                ->required()
                ->numeric(),
            Forms\Components\TextInput::make('unit')
                ->translateLabel()
                ->required(),
            Forms\Components\DatePicker::make('delivery_date')
                ->translateLabel()
                ->default(today())
            ,

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
                ->sortable(),
            Tables\Columns\TextColumn::make('quantity')
                ->alignCenter()
                ->translateLabel()
                ->numeric()
                ->sortable(),
            Tables\Columns\TextColumn::make('unit')
                ->alignCenter()
                ->translateLabel()
                ->searchable(),
            Tables\Columns\TextColumn::make('delivery_date')
                ->alignCenter()
                ->translateLabel()
                ->date()
                ->sortable(),
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
