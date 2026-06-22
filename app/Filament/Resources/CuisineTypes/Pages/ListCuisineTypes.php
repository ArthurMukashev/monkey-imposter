<?php

namespace App\Filament\Resources\CuisineTypes\Pages;

use App\Filament\Resources\CuisineTypes\CuisineTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCuisineTypes extends ListRecords
{
    protected static string $resource = CuisineTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
