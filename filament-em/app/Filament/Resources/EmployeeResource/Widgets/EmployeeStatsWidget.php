<?php

namespace App\Filament\Resources\EmployeeResource\Widgets;

use App\Models\Employee;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class EmployeeStatsWidget extends BaseWidget
{
    protected static ?int $sort = 5;
    protected function getStats(): array
    {
        return [
            Stat::make('Total Employees',Employee::count())
                ->description('all Employye from database')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]),
        ];
    }
}
