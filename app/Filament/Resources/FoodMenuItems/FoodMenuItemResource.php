<?php

namespace App\Filament\Resources\FoodMenuItems;

use App\Filament\Resources\FoodMenuItems\Pages\CreateFoodMenuItem;
use App\Filament\Resources\FoodMenuItems\Pages\EditFoodMenuItem;
use App\Filament\Resources\FoodMenuItems\Pages\ListFoodMenuItems;
use App\Filament\Resources\FoodMenuItems\Schemas\FoodMenuItemForm;
use App\Filament\Resources\FoodMenuItems\Tables\FoodMenuItemsTable;
use App\Models\FoodMenuItem;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FoodMenuItemResource extends Resource
{
    protected static ?string $model = FoodMenuItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $navigationLabel = 'Menu Items';

    protected static string|\UnitEnum|null $navigationGroup = 'Menu';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Menu Item';

    protected static ?string $pluralModelLabel = 'Menu Items';

    public static function form(Schema $schema): Schema
    {
        return FoodMenuItemForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FoodMenuItemsTable::configure($table);
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
            'index' => ListFoodMenuItems::route('/'),
            'create' => CreateFoodMenuItem::route('/create'),
            'edit' => EditFoodMenuItem::route('/{record}/edit'),
        ];
    }
}
