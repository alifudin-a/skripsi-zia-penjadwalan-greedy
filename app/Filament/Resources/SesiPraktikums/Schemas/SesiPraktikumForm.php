<?php

namespace App\Filament\Resources\SesiPraktikums\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SesiPraktikumForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                /**
                 * Nama sesi
                 * contoh:
                 * Sesi 1
                 */
                TextInput::make('nama_sesi')
                    ->required(),

                /**
                 * Jam mulai
                 */
                TextInput::make('jam_mulai')
                    ->type('time')
                    ->required(),

                /**
                 * Jam selesai
                 */
                TextInput::make('jam_selesai')
                    ->type('time')
                    ->required(),

                /**
                 * Total JP sesi
                 */
                TextInput::make('jumlah_jp')
                    ->numeric()
                    ->required(),
            ]);
    }
}
