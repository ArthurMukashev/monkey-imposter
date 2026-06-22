<?php

namespace App\Filament\Resources\Promos\Schemas;

use Filament\Forms\Components\{DateTimePicker, FileUpload, Select, TextInput};
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PromoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('placement')
                    ->options([
                        'home' => 'Главная',
                        'section' => 'Страница раздела',
                        'place-details' => 'Детальная объекта',
                        'kiosk-home' => 'Kiosk старт',
                    ])
                    ->required(),
                TextInput::make('priority')->numeric()->default(0),
                Select::make('section')
                    ->options([
                        'tourism' => 'Туризм',
                        'active' => 'Активный отдых',
                        'gastronomy' => 'Гастрономия',
                    ])
                    ->nullable()
                    ->visible(fn(callable $get) => $get('placement') === 'section'),
                DateTimePicker::make('active_from')->timezone('+05:00'),
                DateTimePicker::make('active_until')->timezone('+05:00')->after('active_from'),
                TextInput::make('title')->required(),
                TextInput::make('teaser')->required(),

                // Target
                Select::make('target_type')
                    ->options([
                        'place' => 'Объект',
                        'section' => 'Раздел',
                        'external' => 'Внешняя ссылка',
                    ])
                    ->reactive(),

                // Условные поля для каждого типа
                TextInput::make('target_slug')
                    ->visible(fn(callable $get) => in_array($get('target_type'), ['place', 'section']))
                    ->required(fn(callable $get) => in_array($get('target_type'), ['place', 'section'])),
                TextInput::make('target_url')
                    ->visible(fn(callable $get) => $get('target_type') === 'external')
                    ->url()
                    ->required(fn(callable $get) => $get('target_type') === 'external'),

                // Изображение
                Section::make('Изображение')
                    ->schema([
                        FileUpload::make('image.url')
                            ->image()
                            ->directory('promos')
                            ->required(),
                        TextInput::make('image.alt')->required(),
                        TextInput::make('image.title')->required(),
                    ]),
            ]);
    }
}
