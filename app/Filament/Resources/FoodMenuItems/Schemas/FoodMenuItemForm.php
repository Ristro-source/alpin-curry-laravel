<?php

namespace App\Filament\Resources\FoodMenuItems\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FoodMenuItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('General Information')
                    ->description('Core details about this menu item.')
                    ->schema([
                        Select::make('food_menu_category_id')
                            ->relationship('category', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->label('Category')
                            ->columnSpanFull(),
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('€')
                            ->default('0.00')
                            ->step('0.01')
                            ->minValue(0),
                        Select::make('status')
                            ->options([
                                'active'   => 'Active',
                                'inactive' => 'Inactive',
                            ])
                            ->required()
                            ->default('active')
                            ->native(false),
                        TextInput::make('order')
                            ->required()
                            ->numeric()
                            ->default(5)
                            ->minValue(1)
                            ->helperText('Lower number = shown first within the category.'),
                    ])
                    ->columns(['default' => 1, 'lg' => 2]),

                Section::make('Descriptions')
                    ->description('Item description shown on the public menu. Provide at least one language.')
                    ->schema([
                        Textarea::make('description_en')
                            ->label('English')
                            ->rows(3),
                        Textarea::make('description_it')
                            ->label('Italiano')
                            ->rows(3),
                        Textarea::make('description_de')
                            ->label('Deutsch')
                            ->rows(3),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Dietary Information')
                    ->description('Tag this item with applicable allergens, intolerances and ingredients for customer transparency.')
                    ->schema([
                        CheckboxList::make('foodAllergies')
                            ->relationship('foodAllergies', 'name')
                            ->label('Allergens (EU 1169/2011)')
                            ->columns(['default' => 2, 'lg' => 4])
                            ->searchable(),
                        CheckboxList::make('foodIntolerances')
                            ->relationship('foodIntolerances', 'name')
                            ->label('Intolerances')
                            ->columns(['default' => 2, 'lg' => 4])
                            ->searchable(),
                        CheckboxList::make('foodIngredients')
                            ->relationship('foodIngredients', 'name')
                            ->label('Key Ingredients')
                            ->columns(['default' => 2, 'lg' => 4])
                            ->searchable(),
                    ])
                    ->collapsible(),
            ]);
    }
}
