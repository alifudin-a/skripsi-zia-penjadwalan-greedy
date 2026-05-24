<?php

namespace App\Filament\Resources\SesiPraktikums\Pages;

use App\Filament\Resources\SesiPraktikums\SesiPraktikumResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSesiPraktikum extends EditRecord
{
    protected static string $resource = SesiPraktikumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
