<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action as Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Livewire;
use Filament\Forms\Components\Radio;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\TextEntry\TextEntrySize;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Blade;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Order Detail')
                    ->icon('heroicon-o-magnifying-glass')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('order_code')->label('Code')->icon('heroicon-s-qr-code')->copyable(),
                        TextEntry::make('order_date')->label('Date')->icon('heroicon-s-calendar-days')->dateTime(),
                        TextEntry::make('customer.name')->label('Customer Name')->icon('heroicon-s-user'),
                        TextEntry::make('customer.phone')->label('Customer Phone')->icon('heroicon-s-phone'),
                        TextEntry::make('status')->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'waiting' => 'gray',
                                'progress' => 'warning',
                                'ready' => 'info',
                                'done' => 'success',
                            }),
                        TextEntry::make('remarks')->icon('heroicon-s-list-bullet'),
                    ]),
                Section::make('Product Details')
                    ->icon('heroicon-o-document-magnifying-glass')
                    ->relationship('details')
                    ->columns(4)
                    ->schema([
                        TextEntry::make('product.name')
                            ->icon('heroicon-s-briefcase')
                            ->label('Products')
                            ->columnSpan(2)
                            ->listWithLineBreaks(),
                        TextEntry::make('qty')
                            ->label('Qty')
                            ->fontFamily(FontFamily::Mono)
                            ->listWithLineBreaks(),
                        TextEntry::make('price')
                            ->label('-')
                            ->alignEnd()
                            ->color('success')
                            ->money('IDR')
                            ->fontFamily(FontFamily::Mono)
                            ->listWithLineBreaks(),
                    ]),
                Section::make('Total')
                    ->description('Grand total of current orders')
                    ->aside()
                    ->icon('heroicon-o-banknotes')
                    ->schema([
                        TextEntry::make('subtotal_price')
                            ->inlineLabel()
                            ->color('success')
                            ->alignEnd()
                            ->fontFamily(FontFamily::Mono)
                            ->money('IDR'),
                        TextEntry::make('total_discount')
                            ->inlineLabel()
                            ->label('Discount')
                            ->color('danger')
                            ->alignEnd()
                            ->fontFamily(FontFamily::Mono)
                            ->formatStateUsing(function ($record) {
                                if ($record->discount_type == 'percentage') {
                                    return '(' . number_format($record->discount_value, 0) . '%) ' . 'IDR ' . number_format($record->total_discount, 2);
                                }
                                return 'IDR ' . number_format($record->total_discount, 2); // Format as currency
                            }),
                        TextEntry::make('total_price')
                            ->inlineLabel()
                            ->fontFamily(FontFamily::Mono)
                            ->size(TextEntrySize::Large)
                            ->weight(FontWeight::ExtraBold)
                            ->color('success')
                            ->alignEnd()
                            ->money('IDR'),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('printReceipt')
                ->label('PDF Print')
                ->color('success')
                ->icon('heroicon-s-arrow-down-tray')
                ->action(function (Order $record) {
                    return response()->streamDownload(function () use ($record) {
                        echo Pdf::loadHtml(
                            Blade::render('download-order', ['record' => $record])
                        )->stream();
                    }, $record->order_code . '.pdf');
                }),
            Action::make('changeStatus')
                ->color('info')
                ->label('Change Status')
                ->icon('heroicon-o-arrows-right-left')
                ->modalHeading('Change order status')
                ->modalIcon('heroicon-o-arrows-right-left')
                ->modalDescription('Change status based on current order process')
                ->modalWidth(MaxWidth::Medium)
                ->action(function (array $data) {
                    // Handle update status logic
                    $this->record->update([
                        'status' => $data['status'],
                    ]);
                })
                ->form([
                    Radio::make('status')
                        ->hiddenLabel()
                        ->options([
                            'waiting'  => 'Waiting',
                            'progress' => 'Progress',
                            'ready'    => 'Ready',
                            // 'done'     => 'Done',
                        ])
                        ->default($this->record->status)
                        ->descriptions([
                            'waiting'  => 'Order that need to be processed',
                            'progress' => 'Order is in progress',
                            'ready'    => 'Order is ready to be picked up',
                            // 'done'     => 'Order is finished',
                        ])
                ]),
            EditAction::make()
                ->icon('heroicon-o-pencil-square')
                ->hidden(function ($data)  {
                    if (auth()->user()->roles[0]['id'] !== 1) {
                        return true;
                    }
                }),
        ];
    }

}
