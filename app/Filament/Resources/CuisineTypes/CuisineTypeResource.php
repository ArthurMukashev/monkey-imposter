<?php

namespace App\Filament\Resources\CuisineTypes;

use App\Filament\Resources\CuisineTypes\Pages\CreateCuisineType;
use App\Filament\Resources\CuisineTypes\Pages\EditCuisineType;
use App\Filament\Resources\CuisineTypes\Pages\ListCuisineTypes;
use App\Filament\Resources\CuisineTypes\Schemas\CuisineTypeForm;
use App\Filament\Resources\CuisineTypes\Tables\CuisineTypesTable;
use App\Models\CuisineType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CuisineTypeResource extends Resource
{
    protected static ?string $model = CuisineType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return CuisineTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CuisineTypesTable::configure($table);
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
            'index' => ListCuisineTypes::route('/'),
            'create' => CreateCuisineType::route('/create'),
            'edit' => EditCuisineType::route('/{record}/edit'),
        ];
    }
}
