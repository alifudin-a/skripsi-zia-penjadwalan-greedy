<?php

namespace App\Filament\Resources\KetersediaanGurus;

use App\Filament\Resources\KetersediaanGurus\Pages\CreateKetersediaanGuru;
use App\Filament\Resources\KetersediaanGurus\Pages\EditKetersediaanGuru;
use App\Filament\Resources\KetersediaanGurus\Pages\ListKetersediaanGurus;
use App\Filament\Resources\KetersediaanGurus\Schemas\KetersediaanGuruForm;
use App\Filament\Resources\KetersediaanGurus\Tables\KetersediaanGurusTable;
use App\Models\KetersediaanGuru;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class KetersediaanGuruResource extends Resource
{
    protected static ?string $model = KetersediaanGuru::class;

    protected static ?string $slug = 'ketersediaan-guru';

    protected static ?string $navigationLabel = 'Ketersediaan Guru';

    protected static ?string $modelLabel = 'Ketersediaan Guru';

    protected static ?string $pluralModelLabel = 'Ketersediaan Guru';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'hari';

    public static function form(Schema $schema): Schema
    {
        return KetersediaanGuruForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KetersediaanGurusTable::configure($table);
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
            'index' => ListKetersediaanGurus::route('/'),
            'create' => CreateKetersediaanGuru::route('/create'),
            'edit' => EditKetersediaanGuru::route('/{record}/edit'),
        ];
    }
}
