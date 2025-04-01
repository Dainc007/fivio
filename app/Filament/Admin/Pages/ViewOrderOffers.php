                    ->options(OfferStatus::withLabels()),
                Tables\Filters\SelectFilter::make('country_origin')
                    ->options(Offer::query()
                        ->distinct()
                        ->pluck('country_origin')
                        ->mapWithKeys(fn ($country) => [$country => $country])),
            ])
            ->actions([
                Tables\Actions\Action::make('accept')
                    ->color(Color::Success)
                    ->visible(fn ($record): bool => $record->status === OfferStatus::PENDING->value)
                    ->action(function (Offer $record): void {
                        $record->order->update(['status' => OrderStatus::FINISHED->value]);
                        $record->update(['status' => OfferStatus::ACCEPTED->value]);
                        $record->user->notify(new OrderAccepted());
                        
                        $this->notify('success', 'Offer accepted successfully');
                        $this->redirect('/orders');
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('accept_selected')
                    ->color(Color::Success)
                    ->action(function (array $recordIds): void {
                        $offers = Offer::whereIn('id', $recordIds)->get();
                        
                        foreach ($offers as $offer) {
                            if ($offer->status === OfferStatus::PENDING->value) {
                                $offer->order->update(['status' => OrderStatus::FINISHED->value]);
                                $offer->update(['status' => OfferStatus::ACCEPTED->value]);
                                $offer->user->notify(new OrderAccepted());
                            }
                        }
                        
                        $this->notify('success', 'Offers accepted successfully');
                        $this->redirect('/orders');
                    }),
            ]);
    }

    protected function getTableQuery(): Builder
    {
        return Offer::query()->with(['user', 'order.product']);
    }
}
