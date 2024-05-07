<?php

namespace App\Filament\Resources\CountryResource\Widgets;

use Filament\Tables;
use App\Models\Country;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestCountryTableWidget extends BaseWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(Country::query())
            ->defaultSort('name','asec')
            ->columns([
                Tables\Columns\TextColumn::make('name'),

            ]);
    }
}
