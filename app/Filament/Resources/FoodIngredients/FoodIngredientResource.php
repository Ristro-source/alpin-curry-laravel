<?php

namespace App\Filament\Resources\FoodIngredients;

use App\Filament\Resources\FoodIngredients\Pages\CreateFoodIngredient;
use App\Filament\Resources\FoodIngredients\Pages\EditFoodIngredient;
use App\Filament\Resources\FoodIngredients\Pages\ListFoodIngredients;
use App\Filament\Resources\FoodIngredients\Schemas\FoodIngredientForm;
use App\Filament\Resources\FoodIngredients\Tables\FoodIngredientsTable;
use App\Models\FoodIngredient;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FoodIngredientResource extends Resource
{
    protected static ?string $model = FoodIngredient::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static ?string $navigationLabel = 'Ingredients';

    protected static string|\UnitEnum|null $navigationGroup = 'Dietary Info';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Ingredient';

    protected static ?string $pluralModelLabel = 'Ingredients';

    public static function form(Schema $schema): Schema
    {
        return FoodIngredientForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FoodIngredientsTable::configure($table);
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
            'index' => ListFoodIngredients::route('/'),
            'create' => CreateFoodIngredient::route('/create'),
            'edit' => EditFoodIngredient::route('/{record}/edit'),
        ];
    }
}
