<?php

namespace App\Filament\Resources\Laboratoriums\Pages;

use App\Filament\Resources\Laboratoriums\LaboratoriumResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLaboratoriums extends ListRecords
{
    protected static string $resource = LaboratoriumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
