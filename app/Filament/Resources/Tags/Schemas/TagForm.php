<?php

namespace App\Filament\Resources\Tags\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components;
use Illuminate\Support\Str;

class TagForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Components\TextInput::make('title')->required()
                    ->afterStateUpdated(fn($state, callable $set) => $set('slug', Str::slug($state))),
                Components\TextInput::make('slug')->required()->unique(ignoreRecord: true),
                Components\ColorPicker::make('color'), // HEX-цвет
            ]);
    }
}
