<?php

namespace App\Filament\Resources\SesiPraktikums\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SesiPraktikumsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('nama_sesi'),

                TextColumn::make('jam_mulai'),

                TextColumn::make('jam_selesai'),

                TextColumn::make('jumlah_jp')
                    ->label('JP'),
            ])

            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
