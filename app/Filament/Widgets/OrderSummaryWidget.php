<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;

class OrderSummaryWidget extends ChartWidget
{
    protected static ?string $heading = 'Total Order Per Day';
    protected static ?string $maxHeight = '300px';
    
    protected function getData(): array
    {
        // Get n days summary
        $num_days = 30;
        $datas = Order::getLastNDaysSummary($num_days);
        return [
            'datasets' => [
                [
                    'label' => 'Order per day',
                    'data' =>  $datas['data']
                ],
            ],
            'labels' => $datas['labels']
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
