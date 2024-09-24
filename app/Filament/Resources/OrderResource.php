<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Faker\Provider\Uuid;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Blade;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Detail')
                    ->icon('heroicon-o-pencil-square')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Hidden::make('order_code')
                            ->required()
                            ->default(Uuid::uuid()),
                        Forms\Components\DateTimePicker::make('order_date')
                            ->required()
                            ->default(now()),
                        Forms\Components\Select::make('status')
                            ->required()
                            ->searchable(['name', 'phone'])
                            ->default('waiting')
                            ->options([
                                'waiting' => 'Waiting',
                                'progress' => 'Progress',
                                'ready' => 'Ready',
                                'done' => 'Done',
                            ]),
                        Forms\Components\Select::make('customer_id')
                            ->name('Customer')
                            ->placeholder('Search customer name')
                            ->relationship('customer', 'name')
                            ->getOptionLabelFromRecordUsing(fn(Customer $q) => "{$q->name} [{$q->phone}]")
                            ->searchable()
                            ->required()
                            ->preload(),
                        Forms\Components\Select::make('worker_id')
                            ->name('Worker')
                            ->required()
                            ->searchable()
                            ->placeholder('Search worker name')
                            ->relationship('worker', 'name')
                            ->preload(),
                        Forms\Components\Textarea::make('remarks')
                            ->columnSpanFull(),
                    ]),
                Section::make('Discount')
                    ->icon('heroicon-o-percent-badge')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('discount_type')
                            ->searchable()
                            ->options([
                                'fixed' => 'Fixed',
                                'percentage' => 'Percentage',
                            ]),
                        Forms\Components\TextInput::make('discount_value')
                            ->numeric()
                            ->default(0),
                    ]),
                Section::make('Order Products')
                    ->icon('heroicon-o-rectangle-stack')
                    ->schema([
                        Repeater::make('products')
                            ->grid(2)
                            ->columns(4)
                            ->addActionLabel('Add more product')
                            ->schema([
                                Select::make('product_id')
                                    ->label('Product')
                                    ->relationship('details.product', 'name')
                                    ->getOptionLabelFromRecordUsing(fn(Product $q) => "{$q->name} [{$q->price}/{$q->unit}]")
                                    ->searchable(['name', 'price', 'unit'])
                                    ->columnSpan(3)
                                    ->required()
                                    ->preload(),
                                TextInput::make('qty')
                                    ->label('Quantity')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->required()
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_date')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('order_code')
                    ->limit(9)
                    ->searchable(),
                TextColumn::make('customer.name')
                    ->sortable(),
                TextColumn::make('worker.name')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'waiting' => 'gray',
                        'progress' => 'warning',
                        'ready' => 'info',
                        'done' => 'success',
                    })
                    ->sortable(),
                TextColumn::make('discount_type')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('discount_value')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('total_discount')
                    ->money('IDR')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('subtotal_price')
                    ->money('IDR')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('total_price')
                    ->money('IDR')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('pdf')
                    ->label('PDF')
                    ->color('success')
                    ->icon('heroicon-s-arrow-down-tray')
                    ->action(function (Order $record) {
                        return response()->streamDownload(function () use ($record) {
                            echo Pdf::loadHtml(
                                Blade::render('download-order', ['record' => $record])
                            )
                            ->setPaper('A8', 'portrait') // Set ukuran receipt
                            ->stream();
                        }, $record->order_code . '.pdf');
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
