<?php

namespace App\Filament\Resources\Laboratoriums;

use App\Filament\Resources\Laboratoriums\Pages\CreateLaboratorium;
use App\Filament\Resources\Laboratoriums\Pages\EditLaboratorium;
use App\Filament\Resources\Laboratoriums\Pages\ListLaboratoriums;
use App\Filament\Resources\Laboratoriums\Schemas\LaboratoriumForm;
use App\Filament\Resources\Laboratoriums\Tables\LaboratoriumsTable;
use App\Models\Laboratorium;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LaboratoriumResource extends Resource
{
    protected static ?string $model = Laboratorium::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $slug = 'laboratorium';

    protected static ?string $navigationLabel = 'Laboratorium';

    protected static ?string $modelLabel = 'Laboratorium';

    protected static ?string $pluralModelLabel = 'Laboratorium';

    public static function form(Schema $schema): Schema
    {
        return LaboratoriumForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LaboratoriumsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLaboratoriums::route('/'),
            'create' => CreateLaboratorium::route('/create'),
            'edit' => EditLaboratorium::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user?->role === 'admin';
    }
}