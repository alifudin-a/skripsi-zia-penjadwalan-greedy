<?php

namespace App\Filament\Resources\KetersediaanGurus\Pages;

use App\Filament\Resources\KetersediaanGurus\KetersediaanGuruResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListKetersediaanGurus extends ListRecords
{
    protected static string $resource = KetersediaanGuruResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
