<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Filament\Resources\EmployeeResource\RelationManagers;
use App\Models\City;
use App\Models\Employee;
use App\Models\State;
use Carbon\Carbon;
use Doctrine\DBAL\Schema\Schema;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
//use Filament\Infolists\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Support\Htmlable;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Employee Management';

    //protected static ?string $recordTitleAttribute='first_name';

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->first_name ." ". $record->middle_name ." ". $record->last_name ;  //return the country of the $recordTitleAttribute upp ^^
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'first_name','last_name','middle_name'
        ];
    }


    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Country'=> $record->country->name,
            'State'=> $record->state->name,
            'City'=> $record->city->name,
            'Department'=> $record->department->name,
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getModel()::count() > 5 ? 'warning' : 'success';
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make('Realtionship')
                ->description('Region information')
                ->schema([
                    Select::make('country_id')
                    ->required()
                    ->relationship(name: 'country',titleAttribute:'name')
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(function (Set $set) {
                        $set('state_id',null);
                        $set('city_id',null);})
                    ->preload(),

                    Select::make('state_id')
                    ->required()
                    ->options(fn (Get $get): Collection => State::query()
                        ->where('country_id',$get('country_id'))
                        ->pluck('name','id'))
                    ->live()
                    ->afterStateUpdated(fn (Set $set) => $set('city_id',null))
                    ->searchable()
                    ->preload(),
                    Select::make('city_id')
                    ->required()
                    ->options(fn (Get $get): Collection => City::query()
                        ->where('state_id',$get('state_id'))
                        ->pluck('name','id'))
                    ->live()
                    ->searchable()
                    ->preload(),

                    Select::make('department_id')
                    ->required()
                    ->relationship(name: 'department',titleAttribute:'name')
                    ->searchable()
                    ->preload(),

                ])->columns(2),

                Section::make('Usen name')
                    ->description('Put the user name details in.')
                    ->schema([
                    TextInput::make('first_name')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('last_name')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('middle_name')
                        ->required()
                        ->maxLength(255),
                    ])->columns(3),

                Section::make('User Address')
                ->schema([
                TextInput::make('address')
                    ->required()
                    ->maxLength(255),
                TextInput::make('zip_code')
                    ->required()
                    ->maxLength(255),
                ])->columns(2),
                Section::make('Date details')
                ->schema([
                DatePicker::make('date_of_birth')
                    ->required()
                    ->native(false)
                    ->displayFormat('d/m/Y'),
                DatePicker::make('date_hired')
                    ->required(),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('country.name')
                    ->numeric()
                    ->sortable(),
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
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('address')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('zip_code')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('date_of_birth')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                SelectFilter::make('Department')
                ->relationship('department','name')
                ->preload()
                ->label('Department Filter')
                ->indicator('Department'),

                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from'),
                        DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );


                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['from'] ?? null) {
                            $indicators[] = Indicator::make('Created from ' . Carbon::parse($data['from'])->toFormattedDateString())
                                ->removeField('from');
                        }

                        if ($data['until'] ?? null) {
                            $indicators[] = Indicator::make('Created until ' . Carbon::parse($data['until'])->toFormattedDateString())
                                ->removeField('until');
                        }

                        return $indicators;
                    })->columnSpan(2)->columns(2),

                ],)// layout: FiltersLayout::AboveContent) ->filtersFormColumns(3)
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Employeeee Deleteeed')
                            ->body('Employee Deleteeed Successfully'))

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }




    public static function getRelations(): array
    {
        return [

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'view' => Pages\ViewEmployee::route('/{record}'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
