<?php

namespace App\Filament\Resources\FoodMenuCategories\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FoodMenuCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('General')
                    ->description('Category settings and display configuration.')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Select::make('type')
                            ->options([
                                'food'  => 'Food',
                                'drink' => 'Drink',
                            ])
                            ->required()
                            ->default('food')
                            ->native(false),
                        Select::make('display_type')
                            ->options([
                                'off'    => 'Off (hidden from menu)',
                                'single' => 'Single View',
                                'dual'   => 'Dual View',
                            ])
                            ->required()
                            ->default('off')
                            ->native(false)
                            ->helperText('Controls how this category is displayed on the public menu.'),
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
                            ->helperText('Lower number = shown first.'),
                    ])
                    ->columns(['default' => 1, 'lg' => 2]),

                Section::make('Descriptions')
                    ->description('Optional category description shown on the menu page.')
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
            ]);
    }
}
