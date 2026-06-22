<?php

namespace App\Filament\Resources\CuisineTypes\Pages;

use App\Filament\Resources\CuisineTypes\CuisineTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCuisineType extends EditRecord
{
    protected static string $resource = CuisineTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
