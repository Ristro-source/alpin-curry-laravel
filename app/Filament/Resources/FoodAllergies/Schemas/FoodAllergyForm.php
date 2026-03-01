<?php

namespace App\Filament\Resources\FoodAllergies\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FoodAllergyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('General')
                    ->description('Basic information about this allergen.')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('key')
                            ->maxLength(255)
                            ->helperText('Internal identifier key, e.g. "gluten", "nuts".'),
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
                    ->description('Provide a description in one or more languages.')
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
