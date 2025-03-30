<?php

declare(strict_types=1);

namespace App\Filament\Auth\Resources;

use App\Filament\Auth\Resources\OrderResource\Pages;
use App\Models\Offer;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
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

        if (! auth()->user()->has_access) {
            $table->heading('Twoje Konto Oczekuje na Weryfikację.');
        }

        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->withExists('userOffers'))
            ->striped()
            ->columns($columns)
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('offer')
                    ->visible(function ($record): bool {
                        return ! $record->userHasSubmittedOffer;
                    })
                    ->icon('heroicon-o-plus')
                    ->color(Color::Fuchsia)

                    ->modal()
                    ->form([
                        Forms\Components\TextInput::make('price')

                            ->columnSpanFull()
                            ->minValue(1)
                            ->required()
                            ->numeric()
                            ->suffix('zł'),
                    ])
                    ->after(function (array $data, $record): void {
                        Offer::create([
                            'price' => $data['price'],
                            'user_id' => auth()->id(),
                            'order_id' => $record->id,
                        ]);

                        Notification::make()
                            ->title('Sukces!')
                            ->body('Oferta została pomyślnie utworzona.')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('offerMade')
                    ->icon('heroicon-o-check-circle')
                    ->color(Color::Green)
                    ->visible(function ($record) {
                        return $record->userHasSubmittedOffer;
                    })
                    ->disabled(),
            ])
            ->bulkActions([
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageOrders::route('/'),
        ];
    }

    /**
     * @return Tables\Columns\TextColumn[]
     */
    public static function getColumns(): array
    {
        $columns = [];

        if (auth()->user()->has_access) {
            return [
                Tables\Columns\TextColumn::make('order_id')
                    ->hidden(),
                Tables\Columns\TextColumn::make('product.name')

                    ->numeric(),
                Tables\Columns\TextColumn::make('quantity')

                    ->numeric(),
                Tables\Columns\TextColumn::make('unit')

                    ->searchable(),
                Tables\Columns\TextColumn::make('delivery_date')

                    ->date(),
                Tables\Columns\TextColumn::make('status')

                    ->badge()

                    ->color(fn ($record): string => match ($record->status) {
                        'active' => 'success',
                        'finished' => 'danger',
                        'cancelled' => 'yellow',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('address.full_address')

                    ->limit(50)
                    ->wrap(),
                Tables\Columns\TextColumn::make('created_at')

                    ->dateTime()

                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')

                    ->dateTime()

                    ->toggleable(isToggledHiddenByDefault: true),
            ];
        }

        return $columns;
    }

    public static function getNavigationLabel(): string
    {
        return __('Orders');
    }
}
