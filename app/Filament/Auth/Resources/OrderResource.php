<?php

namespace App\Filament\Auth\Resources;

use App\Filament\Auth\Resources\OrderResource\Pages;
use App\Filament\Auth\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 1;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([

//                    Forms\Components\Select::make('product_id')
//                        ->relationship('product', 'name')
//                        ->required(),
//                    Forms\Components\TextInput::make('quantity')
//                        ->required()
//                        ->numeric(),
//                    Forms\Components\TextInput::make('price')
//                        ->numeric()
//                        ->prefix('$'),
//                    Forms\Components\TextInput::make('unit')
//                        ->required(),
//                    Forms\Components\DatePicker::make('delivery_date'),
//                    Forms\Components\Textarea::make('attachment')
//                        ->columnSpanFull(),
//                    Forms\Components\TextInput::make('status'),

            ]);
    }

    public static function table(Table $table): Table
    {
        $columns = self::getColumns();

        if(!auth()->user()->has_access) {
            $table->heading('Twoje Konto Oczekuje na Weryfikację.');
        }

        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->where('user_id', auth()->id()))
            ->striped()
            ->columns($columns)
            ->filters([
                //
            ])
            ->actions([
//                Tables\Actions\EditAction::make(),
//                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
//                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
//                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageOrders::route('/'),
        ];
    }

    public static function getColumns()
    {
        $columns = [];

        if(auth()->user()->has_access) {
            $columns = [
                Tables\Columns\TextColumn::make('product.name')
                    ->translateLabel()
                    ->alignCenter()
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->translateLabel()
                    ->alignCenter()
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit')
                    ->translateLabel()
                    ->alignCenter()
                    ->searchable(),
                Tables\Columns\TextColumn::make('delivery_date')
                    ->translateLabel()
                    ->alignCenter()
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->translateLabel()
                    ->alignCenter()
                    ->searchable(),
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
            ];
        }

        return $columns;
    }
}
