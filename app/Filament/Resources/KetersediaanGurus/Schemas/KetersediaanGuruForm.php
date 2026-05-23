<?php

namespace App\Filament\Resources\KetersediaanGurus\Schemas;

use App\Models\Guru;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class KetersediaanGuruForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('guru_id')
                    ->label('Guru')
                    ->options(
                        \App\Models\Guru::pluck('nama', 'id')
                    )
                    ->searchable()
                    ->required(),

                Select::make('hari')
                    ->options([
                        'Senin' => 'Senin',
                        'Selasa' => 'Selasa',
                        'Rabu' => 'Rabu',
                        'Kamis' => 'Kamis',
                        'Jumat' => 'Jumat',
                        'Sabtu' => 'Sabtu',
                    ])
                    ->required(),

                TimePicker::make('jam_mulai')
                    ->seconds(false)
                    ->required(),

                TimePicker::make('jam_selesai')
                    ->seconds(false)
                    ->required(),
            ]);
    }
}
