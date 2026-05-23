<?php

namespace App\Filament\Resources\KetersediaanGurus\Pages;

use App\Filament\Resources\KetersediaanGurus\KetersediaanGuruResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditKetersediaanGuru extends EditRecord
{
    protected static string $resource = KetersediaanGuruResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
