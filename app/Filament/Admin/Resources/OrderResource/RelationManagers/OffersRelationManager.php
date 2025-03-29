<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

final class OffersRelationManager extends RelationManager
{
    protected static string $relationship = 'offers';

    //    public function form(Form $form): Form
    //    {
    //        return $form
    //            ->schema([
    //                Forms\Components\TextInput::make('price')
    //                    ->required()
    //                    ->maxLength(255),
    //            ]);
    //    }

    //    public function table(Table $table): Table
    //    {
    //        return $table
    //            ->recordTitleAttribute('price')
    //            ->columns([
    //                Tables\Columns\TextColumn::make('price'),
    //            ])
    //            ->filters([
    //                //
    //            ])
    //            ->headerActions([
    //                Tables\Actions\CreateAction::make(),
    //            ])
    //            ->actions([
    //                Tables\Actions\EditAction::make(),
    //                Tables\Actions\DeleteAction::make(),
    //            ])
    //            ->bulkActions([
    //                Tables\Actions\BulkActionGroup::make([
    //                    Tables\Actions\DeleteBulkAction::make(),
    //                ]),
    //            ]);
    //    }
}
