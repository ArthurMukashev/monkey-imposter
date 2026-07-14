<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('section')
                    ->label('Категория')
                    ->options([
                        'tourism' => 'Туризм',
                        'active' => 'Активный отдых',
                        'gastronomy' => 'Гастрономия',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('title')
                    ->label('Название')
                    ->required()
                    ->maxLength(255)
                    ->live()
                    ->afterStateUpdated(fn($state, callable $set) => $set('slug', \Str::slug($state))),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\Textarea::make('short_description')
                    ->label('Краткое описание')
                    ->maxLength(255),
                Forms\Components\TextInput::make('sort_order')
                    ->label('Порядок сортировки')
                    ->numeric()
                    ->default(0),
            ]);
    }
}
