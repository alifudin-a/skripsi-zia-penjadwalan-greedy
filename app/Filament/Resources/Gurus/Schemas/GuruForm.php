<?php

namespace App\Filament\Resources\Gurus\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\CheckboxList;

class GuruForm
{


    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nip')
                    ->required(),

                TextInput::make('nama')
                    ->required(),

                TextInput::make('no_hp'),

                Textarea::make('alamat'),

                CheckboxList::make('mataPelajarans')
                    ->relationship('mataPelajarans', 'nama')
                    ->columns(2)
                    ->label('Mata Pelajaran'),
            ]);
    }
}
