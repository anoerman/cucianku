<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;

class WorkerSummaryWidget extends ChartWidget
{
    protected static ?string $heading   = 'Order per worker';
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        // Get n days summary
        $num_days = 30;
        $datas    = Order::getLastNDaysWorkerSummary($num_days);
        return [
            'datasets' => $datas['data'],
            'labels'   => $datas['labels']
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
