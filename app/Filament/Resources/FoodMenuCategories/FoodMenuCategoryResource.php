<?php

namespace App\Filament\Resources\FoodMenuCategories;

use App\Filament\Resources\FoodMenuCategories\Pages\CreateFoodMenuCategory;
use App\Filament\Resources\FoodMenuCategories\Pages\EditFoodMenuCategory;
use App\Filament\Resources\FoodMenuCategories\Pages\ListFoodMenuCategories;
use App\Filament\Resources\FoodMenuCategories\Schemas\FoodMenuCategoryForm;
use App\Filament\Resources\FoodMenuCategories\Tables\FoodMenuCategoriesTable;
use App\Models\FoodMenuCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FoodMenuCategoryResource extends Resource
{
    protected static ?string $model = FoodMenuCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static ?string $navigationLabel = 'Categories';

    protected static string|\UnitEnum|null $navigationGroup = 'Menu';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Category';

    protected static ?string $pluralModelLabel = 'Categories';

    public static function form(Schema $schema): Schema
    {
        return FoodMenuCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FoodMenuCategoriesTable::configure($table);
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
            'index' => ListFoodMenuCategories::route('/'),
            'create' => CreateFoodMenuCategory::route('/create'),
            'edit' => EditFoodMenuCategory::route('/{record}/edit'),
        ];
    }
}
