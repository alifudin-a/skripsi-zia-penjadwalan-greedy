<?php

namespace App\Filament\Resources\Laboratoriums\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LaboratoriumForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->required(),

                TextInput::make('kapasitas')
                    ->numeric()
                    ->default(30)
                    ->required(),

                TextInput::make('lokasi'),
            ]);
    }
}