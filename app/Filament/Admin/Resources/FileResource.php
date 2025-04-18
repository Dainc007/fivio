<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\FileResource\Pages;
use App\Models\File;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

final class FileResource extends Resource
{
    protected static ?string $model = File::class;

    protected static ?string $navigationIcon = 'heroicon-o-document';

    protected static ?int $navigationSort = 6;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //                Forms\Components\TextInput::make('name')
                //                    ->required(),
                //                Forms\Components\TextInput::make('path')
                //                    ->required(),
                //                Forms\Components\TextInput::make('mime_type')
                //                    ->required(),
                //                Forms\Components\TextInput::make('size')
                //                    ->required()
                //                    ->numeric(),
                //                Forms\Components\TextInput::make('disk')
                //                    ->required(),
                //                Forms\Components\TextInput::make('collection'),
                //                Forms\Components\TextInput::make('fileable_type')
                //                    ->required(),
                //                Forms\Components\TextInput::make('fileable_id')
                //                    ->required()
                //                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('path')
                    ->searchable(),
                Tables\Columns\TextColumn::make('mime_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('size')
                    ->numeric(),
                Tables\Columns\TextColumn::make('disk')
                    ->searchable(),
                Tables\Columns\TextColumn::make('collection')
                    ->searchable(),
                Tables\Columns\TextColumn::make('fileable_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('fileable_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
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
            'index' => Pages\ManageFiles::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return self::getModel()::count();
    }

    public static function getNavigationLabel(): string
    {
        return __('Files');
    }
}
