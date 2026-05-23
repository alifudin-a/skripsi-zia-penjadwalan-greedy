<?php

namespace App\Filament\Resources\MataPelajarans\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MataPelajaranForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('kode')
                    ->required()
                    ->unique(ignoreRecord: true),

                TextInput::make('nama')
                    ->required()
                    ->maxLength(255),

                TextInput::make('total_jp')
                    ->numeric()
                    ->required()
                    ->default(20),

                TextInput::make('maksimal_jp_per_sesi')
                    ->numeric()
                    ->required()
                    ->default(8),
            ]);
    }
}