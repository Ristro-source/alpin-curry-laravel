<?php

namespace App\Filament\Resources\FoodIntolerances;

use App\Filament\Resources\FoodIntolerances\Pages\CreateFoodIntolerance;
use App\Filament\Resources\FoodIntolerances\Pages\EditFoodIntolerance;
use App\Filament\Resources\FoodIntolerances\Pages\ListFoodIntolerances;
use App\Filament\Resources\FoodIntolerances\Schemas\FoodIntoleranceForm;
use App\Filament\Resources\FoodIntolerances\Tables\FoodIntolerancesTable;
use App\Models\FoodIntolerance;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FoodIntoleranceResource extends Resource
{
    protected static ?string $model = FoodIntolerance::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedExclamationCircle;

    protected static ?string $navigationLabel = 'Intolerances';

    protected static string|\UnitEnum|null $navigationGroup = 'Dietary Info';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Intolerance';

    protected static ?string $pluralModelLabel = 'Intolerances';

    public static function form(Schema $schema): Schema
    {
        return FoodIntoleranceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FoodIntolerancesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFoodIntolerances::route('/'),
            'create' => CreateFoodIntolerance::route('/create'),
            'edit' => EditFoodIntolerance::route('/{record}/edit'),
        ];
    }
}
