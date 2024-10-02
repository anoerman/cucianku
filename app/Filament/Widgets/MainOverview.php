<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MainOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $num_days = 30;
        return [
            Stat::make('Total Orders', number_format(Order::query()->count(), 0, ',', '.'))
                ->icon('heroicon-s-archive-box')
                ->color('success')
                ->description('Total order in the last ' . $num_days . ' days'),
            Stat::make('Total Income', number_format(Order::getLastNDaysIncome($num_days), 0, ',', '.'))
                ->icon('heroicon-s-banknotes')
                ->color('success')
                ->description('Total income in the last ' . $num_days . ' days'),
            Stat::make('Total Customers', number_format(Customer::query()->count(), 0, ',', '.'))
                ->icon('heroicon-s-users')
                ->color('success')
                ->description('Total overall customers'),
        ];
    }
}
