<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ProductResource\Pages;
use App\Filament\Admin\Resources\ProductResource\RelationManagers;
use App\Models\Category;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $icon = 'heroicon-o-cube';
    protected static ?int $navigationSort = 3;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->translateLabel()
                    ->required(),
//                Forms\Components\TextInput::make('price')
//                    ->translateLabel()
//                    ->required()
//                    ->numeric()
//                    ->prefix('$'),
                Forms\Components\Select::make('category_id')
                    ->translateLabel()
                    ->searchable()
                    ->preload()
                    ->relationship('category', 'name')
                    ->required()
                    ->createOptionModalHeading('Create New Category')
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->translateLabel()
                            ->required()
                            ->placeholder('Enter category name')
                            ->label('Category Name'),
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->translateLabel()
                    ->alignCenter()
                    ->searchable()
                    ->toggleable(),
//                Tables\Columns\TextColumn::make('price')
//                    ->translateLabel()
//                    ->alignCenter()
//                    ->money()
//                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->translateLabel()
                    ->alignCenter()
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->translateLabel()
                    ->alignCenter()
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->translateLabel()
                    ->alignCenter()
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->translateLabel()
                    ->multiple()
                    ->preload()
                    ->options(Category::all()->pluck('name', 'id')->toArray())
                    ->attribute('category_id')
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
            'index' => Pages\ManageProducts::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

}
