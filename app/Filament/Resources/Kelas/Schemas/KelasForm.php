<?php

namespace App\Filament\Resources\Kelas\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class KelasForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                /**
                 * Nama kelas
                 * contoh:
                 * X TKJ 1
                 */
                TextInput::make('nama_kelas')
                    ->required(),

                /**
                 * Jurusan
                 * contoh:
                 * TKJ
                 * RPL
                 */
                TextInput::make('jurusan')
                    ->required(),

                /**
                 * Tingkat
                 * contoh:
                 * X
                 * XI
                 * XII
                 */
                TextInput::make('tingkat')
                    ->required(),
            ]);
    }
}