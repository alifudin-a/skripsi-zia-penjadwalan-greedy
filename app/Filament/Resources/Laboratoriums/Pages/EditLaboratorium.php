<?php

namespace App\Filament\Resources\Laboratoriums\Pages;

use App\Filament\Resources\Laboratoriums\LaboratoriumResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLaboratorium extends EditRecord
{
    protected static string $resource = LaboratoriumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
