<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use App\Models\Employee;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestEmployeesTable extends BaseWidget
{

    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 4;
    public function table(Table $table): Table
    {



        return $table
            ->query(Employee::query())
            ->defaultSort('created_at','desc')
            ->columns([
                Tables\Columns\TextColumn::make('country.name'),
                Tables\Columns\TextColumn::make('first_name'),
                Tables\Columns\TextColumn::make('last_name'),
                Tables\Columns\TextColumn::make('created_at')
                ->date('Y-F-d H:i:sA'),
            ]);
    }
}
