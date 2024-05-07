<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class UserStatsWidget extends BaseWidget
{
    protected static ?int $sort = 6;
    protected function getStats(): array
    {
        return [
            Stat::make('Total Users',User::count())
                ->description('all users from databse')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]),
        ];
    }
}
