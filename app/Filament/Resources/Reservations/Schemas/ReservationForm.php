<?php

namespace App\Filament\Resources\Reservations\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ReservationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Guest Details')
                    ->description('Information provided by the guest when booking.')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->tel()
                            ->required()
                            ->maxLength(40),
                        TextInput::make('guests')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(50)
                            ->label('Number of Guests'),
                    ])
                    ->columns(['default' => 1, 'lg' => 2]),

                Section::make('Reservation Details')
                    ->description('Date, time, and any special requests from the guest.')
                    ->schema([
                        DatePicker::make('reservation_date')
                            ->required()
                            ->native(false)
                            ->label('Date'),
                        TimePicker::make('reservation_time')
                            ->required()
                            ->seconds(false)
                            ->label('Time'),
                        Textarea::make('message')
                            ->label('Guest Message / Special Requests')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(['default' => 1, 'lg' => 2]),

                Section::make('Status & Source')
                    ->description('Manage the reservation status and track its origin.')
                    ->schema([
                        Select::make('status')
                            ->options([
                                'pending'   => 'Pending',
                                'confirmed' => 'Confirmed',
                                'cancelled' => 'Cancelled',
                                'no_show'   => 'No Show',
                            ])
                            ->required()
                            ->default('pending')
                            ->native(false),
                        TextInput::make('source')
                            ->label('Booking Source')
                            ->maxLength(20)
                            ->helperText('How this reservation was made (e.g. website, phone, walk-in).'),
                    ])
                    ->columns(['default' => 1, 'lg' => 2]),
            ]);
    }
}
