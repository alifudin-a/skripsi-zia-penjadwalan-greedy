<?php

namespace App\Filament\Resources\Jadwals;

use App\Filament\Resources\Jadwals\Pages\CreateJadwal;
use App\Filament\Resources\Jadwals\Pages\EditJadwal;
use App\Filament\Resources\Jadwals\Pages\ListJadwals;
use App\Filament\Resources\Jadwals\Schemas\JadwalForm;
use App\Filament\Resources\Jadwals\Tables\JadwalsTable;
use App\Models\Jadwal;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class JadwalResource extends Resource
{
    protected static ?string $model = Jadwal::class;

    protected static ?string $slug = 'jadwal';

    protected static ?string $navigationLabel = 'Jadwal';

    protected static ?string $modelLabel = 'Jadwal';

    protected static ?string $pluralModelLabel = 'Jadwal';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'kelas';

    public static function form(Schema $schema): Schema
    {
        return JadwalForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return JadwalsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListJadwals::route('/'),
            'create' => CreateJadwal::route('/create'),
            'edit' => EditJadwal::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user?->role === 'admin';
    }
}
