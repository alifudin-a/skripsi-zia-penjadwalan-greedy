<?php

namespace App\Filament\Pages;

use App\Models\Jadwal;
use App\Models\User;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class JadwalSaya extends Page implements HasTable
{
    use InteractsWithTable;

    /**
     * Icon sidebar
     */
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar';

    /**
     * Judul menu
     */
    protected static ?string $navigationLabel = 'Jadwal Saya';

    /**
     * Judul halaman
     */
    protected ?string $heading = 'Jadwal Praktikum Saya';

    /**
     * Blade view
     */
    protected string $view = 'filament.pages.jadwal-saya';

    /**
     * Hanya guru yang boleh akses
     */
    public static function canAccess(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user?->role === 'guru';
    }

    /**
     * Table jadwal guru login
     */
    public function table(Table $table): Table
    {
        /** @var User|null $user */
        $user = Auth::user();

        /**
         * Ambil data guru dari user login
         */
        $guru = $user?->guru;

        return $table
            ->query(

                Jadwal::query()

                    /**
                     * Hanya jadwal guru login
                     */
                    ->where('guru_id', $guru?->id)

                    /**
                     * Eager loading relasi
                     */
                    ->with([
                        'mataPelajaran',
                        'laboratorium',
                        'kelasRelasi',
                        'sesiPraktikum',
                    ])
            )

            ->columns([

                /**
                 * Mata pelajaran
                 */
                TextColumn::make('mataPelajaran.nama')
                    ->label('Mata Pelajaran'),

                /**
                 * Kelas
                 */
                TextColumn::make('kelasRelasi.nama_kelas')
                    ->label('Kelas'),

                /**
                 * Hari
                 */
                TextColumn::make('hari'),

                /**
                 * Jam mulai
                 */
                TextColumn::make('sesiPraktikum.jam_mulai')
                    ->label('Jam Mulai'),

                /**
                 * Jam selesai
                 */
                TextColumn::make('sesiPraktikum.jam_selesai')
                    ->label('Jam Selesai'),

                /**
                 * Laboratorium
                 */
                TextColumn::make('laboratorium.nama')
                    ->label('Laboratorium'),
            ]);
    }
}
