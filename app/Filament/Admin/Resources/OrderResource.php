<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\OfferResource\Pages;
use App\Filament\Admin\Resources\OfferResource\RelationManagers;
use App\Models\Category;
use App\Models\Offer;
use App\Models\Product;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Mail;
use Psy\Util\Str;

class OfferResource extends Resource
{
    protected static ?string $model = Offer::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->searchable()
                    ->options(Product::all()->pluck('name', 'id'))
                    ->relationship('product', 'name')
                    ->required()
                    ->createOptionModalHeading('Create New Product')
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->placeholder('Enter product name')
                            ->label('Product Name'),
                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->label('Product Price'),
                        Forms\Components\Select::make('category_id')
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
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('unit')
                    ->required(),
                Forms\Components\DatePicker::make('delivery_date'),

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

//                Forms\Components\Select::make('address_id')
//                    ->label('Offer Address')
//                    ->searchable()
//                    ->createOptionForm([
//                        Forms\Components\TextInput::make('street')
//                            ->label('Street Address'),
//                        Forms\Components\TextInput::make('street_additional')
//                            ->label('Additional Address Line'),
//                        Forms\Components\TextInput::make('city')
//                            ->label('City'),
//                        Forms\Components\TextInput::make('postal_code')
//                            ->label('Postal Code'),
//                        Forms\Components\TextInput::make('country')
//                            ->label('Country'),
//                    ])
//                    ->createOptionModalHeading('Create New Address')
//                    ->helperText('Select an existing address or create a new one')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit')
                    ->searchable(),
                Tables\Columns\TextColumn::make('delivery_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
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
            'index' => Pages\ManageOffers::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
