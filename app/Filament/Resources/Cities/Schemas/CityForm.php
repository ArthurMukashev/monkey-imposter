<?php

namespace App\Filament\Resources\Cities\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components;

class CityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Components\TextInput::make('title')->required()->live()
                    ->afterStateUpdated(fn($state, callable $set) => $set('slug', \Str::slug($state))),
                Components\TextInput::make('slug')->required()->unique(ignoreRecord: true),
                Components\TextInput::make('sort_order')->numeric()->default(0),
            ]);
    }
}
