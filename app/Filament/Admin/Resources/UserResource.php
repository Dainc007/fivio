<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserResource\Pages;
use App\Filament\Admin\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $icon = 'heroicon-o-users';
    protected static ?int $navigationSort = 2;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->translateLabel()
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->translateLabel()
                    ->email()
                    ->required(),
                Forms\Components\TextInput::make('company_name')->translateLabel()
                ,
                Forms\Components\TextInput::make('tax_number')->translateLabel()
                ,
                Forms\Components\Toggle::make('has_access')->translateLabel()
                    ->required(),
                Forms\Components\Toggle::make('is_admin')
                    ->required(),
                Forms\Components\TextInput::make('password')->translateLabel()
                    ->visibleOn('create')
                    ->columnSpanFull()
                    ->password()
                    ->required(),
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
                    ->searchable(),
                Tables\Columns\TextColumn::make('company_name')
                    ->alignCenter()
                    ->translateLabel()
                    ->searchable(),
                Tables\Columns\TextColumn::make('tax_number')
                    ->alignCenter()
                    ->translateLabel()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->alignCenter()
                    ->translateLabel()
                    ->searchable(),

                Tables\Columns\ToggleColumn::make('has_access')
                    ->translateLabel()
                    ->alignCenter()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_admin')
                    ->translateLabel()
                    ->alignCenter()
                    ->boolean()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->translateLabel()
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->translateLabel()
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
            'index' => Pages\ManageUsers::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

}
