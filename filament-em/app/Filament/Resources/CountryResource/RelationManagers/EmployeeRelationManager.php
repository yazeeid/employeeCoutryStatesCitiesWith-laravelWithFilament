<?php

namespace App\Filament\Resources\CountryResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Set;
use Filament\Forms\Get;
use Illuminate\Support\Collection;
use App\Models\State;
use App\Models\City;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmployeeRelationManager extends RelationManager
{
    protected static string $relationship = 'employee';

    public function form(Form $form): Form
    {
        return $form
        ->schema([

            Forms\Components\Section::make('Realtionship')
            ->description('Region information')
            ->schema([
                Forms\Components\Select::make('country_id')
                    ->required()
                    ->relationship(name: 'country',titleAttribute:'name')
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(function (Set $set) {
                        $set('state_id',null);
                        $set('city_id',null);})
                    ->preload(),

                Forms\Components\Select::make('state_id')
                    ->required()
                    ->options(fn (Get $get): Collection => State::query()
                        ->where('country_id',$get('country_id'))
                        ->pluck('name','id'))
                    ->live()
                    ->afterStateUpdated(fn (Set $set) => $set('city_id',null))
                    ->searchable()
                    ->preload(),

                Forms\Components\Select::make('city_id')
                    ->required()
                    ->options(fn (Get $get): Collection => City::query()
                        ->where('state_id',$get('state_id'))
                        ->pluck('name','id'))
                    ->live()
                    ->searchable()
                    ->preload(),

                Forms\Components\Select::make('department_id')
                    ->required()
                    ->relationship(name: 'department',titleAttribute:'name')
                    ->searchable()
                    ->preload(),

            ])->columns(2),

            Forms\Components\Section::make('Usen name')
                ->description('Put the user name details in.')
                ->schema([
                    Forms\Components\TextInput::make('first_name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('last_name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('middle_name')
                        ->required()
                        ->maxLength(255),
                ])->columns(3),

            Forms\Components\Section::make('User Address')
                ->schema([
                    Forms\Components\TextInput::make('address')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('zip_code')
                        ->required()
                        ->maxLength(255),
                ])->columns(2),
            Forms\Components\Section::make('Date details')
                ->schema([
                    Forms\Components\DatePicker::make('date_of_birth')
                        ->required()
                        ->native(false)
                    ->displayFormat('d/m/Y'),
                    Forms\Components\DatePicker::make('date_hired')
                        ->required(),
                ])->columns(2),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('first_name')
            ->columns([
                TextColumn::make('state.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('city.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('department.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('first_name')
                    ->searchable(),
                TextColumn::make('last_name')
                    ->searchable(),
                TextColumn::make('middle_name')
                    ->searchable(),
                TextColumn::make('address')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('zip_code')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('date_of_birth')
                    ->date()
                    ->sortable(),
                TextColumn::make('date_hired')
                    ->date()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
