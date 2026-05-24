<?php

namespace App\Filament\Resources\MataPelajarans;

use App\Filament\Resources\MataPelajarans\Pages\CreateMataPelajaran;
use App\Filament\Resources\MataPelajarans\Pages\EditMataPelajaran;
use App\Filament\Resources\MataPelajarans\Pages\ListMataPelajarans;
use App\Filament\Resources\MataPelajarans\Schemas\MataPelajaranForm;
use App\Filament\Resources\MataPelajarans\Tables\MataPelajaransTable;
use App\Models\MataPelajaran;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class MataPelajaranResource extends Resource
{
    protected static ?string $model = MataPelajaran::class;

    protected static ?string $slug = 'mata-pelajaran';

    protected static ?string $navigationLabel = 'Mata Pelajaran';

    protected static ?string $modelLabel = 'Mata Pelajaran';

    protected static ?string $pluralModelLabel = 'Mata Pelajaran';

    // protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $recordTitleAttribute = 'nama';

    public static function form(Schema $schema): Schema
    {
        return MataPelajaranForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MataPelajaransTable::configure($table);
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
            'index' => ListMataPelajarans::route('/'),
            'create' => CreateMataPelajaran::route('/create'),
            'edit' => EditMataPelajaran::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user?->role === 'admin';
    }
}
