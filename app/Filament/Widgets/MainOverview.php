<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MainOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $num_days = 30;
        return [
            Stat::make('Total Orders', Order::query()->count())
                ->icon('heroicon-s-archive-box')
                ->color('success')
                ->description('Total order in the last ' . $num_days . ' days'),
            Stat::make('Total Income', number_format(Order::getLastNDaysIncome($num_days), 0, ',', '.'))
                ->icon('heroicon-s-banknotes')
                ->color('success')
                ->description('Total income in the last ' . $num_days . ' days'),
        ];
    }
}
