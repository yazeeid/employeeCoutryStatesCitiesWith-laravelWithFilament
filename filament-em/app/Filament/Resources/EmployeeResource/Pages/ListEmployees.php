<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use Filament\Actions;
use App\models\Employee;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\EmployeeResource;
use App\Filament\Resources\EmployeeResource\Widgets\EmployeeStatsWidget;


class ListEmployees extends ListRecords
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }



    public function getTabs(): array
    {
        return[

            'All' => Tab::make()
                ->badge(Employee::query()->where('date_hired','<=',now())->count()),
            'This Week' => Tab::make()
                ->modifyQueryUsing(function (Builder $query) {
                    return $query->where('date_hired','>=',now()->subWeek());
                })
                ->badge(Employee::query()->where('date_hired','>=',now()->subWeek())->count()),
            'This Month' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('date_hired','>=' ,now()->subMonth()))
                ->badge(Employee::query()->where('date_hired','>=',now()->subMonth())->count()),
            '6 Years ago' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('date_hired','>=' ,now()->subYears(6)))
                ->badge(Employee::query()->where('date_hired','>=',now()->subYears(6))->count()),
            '10 Years ago' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('date_hired','>=' ,now()->subYears(10)))
                ->badge(Employee::query()->where('date_hired','>=',now()->subYears(10))->count()),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            EmployeeStatsWidget::class
        ];
    }
}
