<?php

namespace App\Filament\Resources\KetersediaanGurus\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class KetersediaanGurusTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('guru.nama')
                    ->label('Guru')
                    ->searchable(),

                TextColumn::make('hari'),

                TextColumn::make('jam_mulai')
                    ->time(),

                TextColumn::make('jam_selesai')
                    ->time(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}