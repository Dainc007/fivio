<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AddressResource\Pages;
use App\Models\Address;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

final class AddressResource extends Resource
{
    protected static ?string $model = Address::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?int $navigationSort = 5;

    protected static bool $shouldRegisterNavigation = false;

    private static ?string $icon = 'heroicon-o-map-pin';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->translateLabel()
                    ->relationship('user', 'name'),
                Forms\Components\Select::make('order_id')
                    ->translateLabel()
                    ->relationship('order', 'id'),
                Forms\Components\TextInput::make('street')
                    ->translateLabel(),
                Forms\Components\TextInput::make('street_additional')->translateLabel(),
                Forms\Components\TextInput::make('city')->translateLabel(),
                Forms\Components\TextInput::make('postal_code')->translateLabel(),
                Forms\Components\TextInput::make('country')->translateLabel(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->translateLabel()
                    ->alignCenter()
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('order.id')
                    ->translateLabel()
                    ->alignCenter()
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('street')
                    ->translateLabel()
                    ->alignCenter()
                    ->searchable(),
                Tables\Columns\TextColumn::make('street_additional')
                    ->translateLabel()
                    ->alignCenter()
                    ->searchable(),
                Tables\Columns\TextColumn::make('city')
                    ->translateLabel()
                    ->alignCenter()
                    ->searchable(),
                Tables\Columns\TextColumn::make('postal_code')
                    ->translateLabel()
                    ->alignCenter()
                    ->searchable(),
                Tables\Columns\TextColumn::make('country')
                    ->translateLabel()
                    ->alignCenter()
                    ->searchable(),
                Tables\Columns\TextColumn::make('latitude')
                    ->translateLabel()
                    ->alignCenter()
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('longitude')
                    ->translateLabel()
                    ->alignCenter()
                    ->numeric()
                    ->sortable(),
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
            'index' => Pages\ManageAddresses::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return self::getModel()::count();
    }
}
