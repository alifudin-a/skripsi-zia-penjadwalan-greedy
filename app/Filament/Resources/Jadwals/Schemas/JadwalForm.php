<?php

namespace App\Filament\Resources\Jadwals\Schemas;

use App\Models\Guru;
use App\Models\Laboratorium;
use App\Models\MataPelajaran;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use App\Models\Jadwal;
use Filament\Forms\Get;

class JadwalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('guru_id')
                    ->label('Guru')
                    ->options(Guru::pluck('nama', 'id'))
                    ->searchable()
                    ->required(),

                Select::make('mata_pelajaran_id')
                    ->label('Mata Pelajaran')
                    ->options(MataPelajaran::pluck('nama', 'id'))
                    ->searchable()
                    ->required(),

                Select::make('laboratorium_id')
                    ->label('Laboratorium')
                    ->options(Laboratorium::pluck('nama', 'id'))
                    ->searchable()
                    ->required(),

                TextInput::make('kelas')
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

                TextInput::make('jam_mulai')
                    ->type('time')
                    ->rule(function (callable $get) {
                        return function (string $attribute, $value, \Closure $fail) use ($get) {

                            $guruId = $get('guru_id');
                            $hari = $get('hari');
                            $jamMulai = $get('jam_mulai');
                            $jamSelesai = $value;

                            if (! $guruId || ! $hari || ! $jamMulai || ! $jamSelesai) {
                                return;
                            }

                            $bentrok = Jadwal::query()
                                ->where('guru_id', $guruId)
                                ->where('hari', $hari)
                                ->where(function ($q) use ($jamMulai, $jamSelesai) {

                                    $q->where('jam_mulai', '<', $jamSelesai)
                                        ->where('jam_selesai', '>', $jamMulai);
                                })
                                ->exists();

                            if ($bentrok) {
                                $fail('Guru sudah memiliki jadwal di jam tersebut.');
                            }
                        };
                    })
                    ->required(),

                TextInput::make('jam_selesai')
                    ->type('time')
                    ->required(),

                Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                    ])
                    ->default('draft')
                    ->required(),
            ]);
    }
}
