<?php

namespace App\Filament\Resources\FoodAllergies;

use App\Filament\Resources\FoodAllergies\Pages\CreateFoodAllergy;
use App\Filament\Resources\FoodAllergies\Pages\EditFoodAllergy;
use App\Filament\Resources\FoodAllergies\Pages\ListFoodAllergies;
use App\Filament\Resources\FoodAllergies\Schemas\FoodAllergyForm;
use App\Filament\Resources\FoodAllergies\Tables\FoodAllergiesTable;
use App\Models\FoodAllergy;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FoodAllergyResource extends Resource
{
    protected static ?string $model = FoodAllergy::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedExclamationTriangle;

    protected static ?string $navigationLabel = 'Allergies';

    protected static string|\UnitEnum|null $navigationGroup = 'Dietary Info';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Allergy';

    protected static ?string $pluralModelLabel = 'Allergies';

    public static function form(Schema $schema): Schema
    {
        return FoodAllergyForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FoodAllergiesTable::configure($table);
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
            'index' => ListFoodAllergies::route('/'),
            'create' => CreateFoodAllergy::route('/create'),
            'edit' => EditFoodAllergy::route('/{record}/edit'),
        ];
    }
}
