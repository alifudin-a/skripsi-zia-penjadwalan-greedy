<?php

namespace App\Filament\Resources\SesiPraktikums;

use App\Filament\Resources\SesiPraktikums\Pages\CreateSesiPraktikum;
use App\Filament\Resources\SesiPraktikums\Pages\EditSesiPraktikum;
use App\Filament\Resources\SesiPraktikums\Pages\ListSesiPraktikums;
use App\Filament\Resources\SesiPraktikums\Schemas\SesiPraktikumForm;
use App\Filament\Resources\SesiPraktikums\Tables\SesiPraktikumsTable;
use App\Models\SesiPraktikum;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class SesiPraktikumResource extends Resource
{
    protected static ?string $model = SesiPraktikum::class;

    protected static ?string $slug = 'sesi-praktikum';

    protected static ?string $navigationLabel = 'Sesi Praktikum';

    protected static ?string $modelLabel = 'Sesi Praktikum';

    protected static ?string $pluralModelLabel = 'Sesi Praktikum';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'nama_sesi';

    public static function form(Schema $schema): Schema
    {
        return SesiPraktikumForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SesiPraktikumsTable::configure($table);
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
            'index' => ListSesiPraktikums::route('/'),
            'create' => CreateSesiPraktikum::route('/create'),
            'edit' => EditSesiPraktikum::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user?->role === 'admin';
    }
}
