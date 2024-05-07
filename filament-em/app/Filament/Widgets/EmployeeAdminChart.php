<?php

namespace App\Filament\Widgets;

use App\Models\Employee;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;

class EmployeeAdminChart extends ChartWidget
{
    protected static ?string $heading = 'Employee Charts';
    protected static string $color = 'warning';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $data = Trend::model(Employee::class)
        ->between(
            start: now()->startOfMonth(),
            end: now()->endOfMonth(),
        )
        ->perDay()
        ->count();

    return [
        'datasets' => [
            [
                'label' => 'Employee',
                'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
            ],
        ],
        'labels' => $data->map(fn (TrendValue $value) => $value->date),
    ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
