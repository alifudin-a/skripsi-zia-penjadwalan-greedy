<?php

namespace App\Filament\Resources\Jadwals\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Services\GreedySchedulerService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class JadwalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('guru.nama')
                    ->label('Guru'),

                TextColumn::make('mataPelajaran.nama')
                    ->label('Mata Pelajaran'),

                TextColumn::make('laboratorium.nama')
                    ->label('Laboratorium'),

                TextColumn::make('kelas.nama_kelas')
                    ->label('Kelas'),

                TextColumn::make('hari'),

                TextColumn::make('sesiPraktikum.jam_mulai')
                    ->label('Jam Mulai'),

                TextColumn::make('sesiPraktikum.jam_selesai')
                    ->label('Jam Selesai'),

                TextColumn::make('status')
                    ->badge(),
            ])
            ->headerActions([
                Action::make('generate_jadwal')
                    ->label('Generate Jadwal')
                    ->icon('heroicon-o-bolt')
                    ->color('success')
                    ->action(function () {

                        // hapus jadwal lama
                        \App\Models\Jadwal::truncate();

                        // generate ulang
                        app(GreedySchedulerService::class)->generate();

                        Notification::make()
                            ->title('Jadwal berhasil digenerate ulang')
                            ->success()
                            ->send();
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
