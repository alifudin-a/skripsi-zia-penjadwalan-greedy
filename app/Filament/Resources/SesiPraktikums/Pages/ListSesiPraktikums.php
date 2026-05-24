<?php

namespace App\Filament\Resources\SesiPraktikums\Pages;

use App\Filament\Resources\SesiPraktikums\SesiPraktikumResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSesiPraktikums extends ListRecords
{
    protected static string $resource = SesiPraktikumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
