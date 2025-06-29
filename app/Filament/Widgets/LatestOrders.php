<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestOrders extends BaseWidget
{
    // Column display
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 2;
    public function table(Table $table): Table
    {
        return $table
            ->query( OrderResource::getEloquentQuery() )
            ->defaultPaginationPageOption(5)
            ->defaultSort('created_at', 'desc')
            ->columns([

                Tables\Columns\TextColumn::make('id')
                    ->label('Order Id')
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('User Name')
                    ->sortable(),

                Tables\Columns\TextColumn::make('grand_total')
                    ->money('RUB'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')->badge()
                    ->color( fn (string $state) : string => match ($state) {
                        'new' => 'info',
                        'processing' => 'warning',
                        'shipped' => 'success',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                    })
                    ->icon(fn (string $state) : string => match ($state) {
                        'new' => 'heroicon-m-sparkles',
                        'processing' => 'heroicon-m-arrow-path',
                        'shipped' => 'heroicon-m-truck',
                        'delivered' => 'heroicon-m-check-badge',
                        'cancelled' => 'heroicon-m-x-circle',
                    }),

                Tables\Columns\TextColumn::make('shipping_method'),

                Tables\Columns\TextColumn::make('payment_method')
                    ->sortable()
                    ->badge(),

                TextColumn::make('created_at')
                    ->label('Ordered at')
                    ->sortable()
                    ->dateTime(),
            ])
            ->Actions([
                Tables\Actions\Action::make('View order')
                    ->url( fn(Order $record) : string => OrderResource::getUrl('view', compact('record')))
                    ->openUrlInNewTab()
                    ->icon('heroicon-s-eye'),
            ]);
    }
}
