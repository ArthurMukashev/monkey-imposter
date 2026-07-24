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
                    ->label('Где разместить')
                    ->options([
                        'home' => 'Главная',
                        'section' => 'Страница раздела',
                        'place-details' => 'Детальная объекта',
                        'kiosk-home' => 'Kiosk старт',
                    ])
                    ->required(),
                TextInput::make('priority')
                    ->label('Порядок размещения (приоритет)')
                    ->numeric()
                    ->default(0),
                Select::make('section')
                    ->label('Категория')
                    ->options([
                        'tourism' => 'Туризм',
                        'active' => 'Активный отдых',
                        'gastronomy' => 'Гастрономия',
                    ])
                    ->nullable()
                    ->visible(fn(callable $get) => $get('placement') === 'section'),
                DateTimePicker::make('active_from')
                    ->label('Активно с')
                    ->timezone('+05:00'),
                DateTimePicker::make('active_until')
                    ->label('Активно до')
                    ->timezone('+05:00')
                    ->after('active_from'),
                TextInput::make('title')
                    ->label('Название')
                    ->required(),
                TextInput::make('teaser')
                    ->label('Тизер')
                    ->required(),

                Select::make('target_type')
                    ->label('Тип объекта')
                    ->options([
                        'place' => 'Объект',
                        'section' => 'Раздел',
                        'external' => 'Внешняя ссылка',
                    ])
                    ->required()
                    ->reactive(),
                TextInput::make('target_slug')
                    ->visible(fn(callable $get) => in_array($get('target_type'), ['place', 'section']))
                    ->required(fn(callable $get) => in_array($get('target_type'), ['place', 'section'])),
                TextInput::make('target_url')
                    ->visible(fn(callable $get) => $get('target_type') === 'external')
                    ->url()
                    ->required(fn(callable $get) => $get('target_type') === 'external'),

                Section::make('Изображение')
                    ->schema([
                        FileUpload::make('image_file')
                            ->label('Загрузить изображение')
                            ->image()
                            ->disk('public')
                            ->directory('promos')
                            ->visibility('public')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
                            ->maxSize(5120)
                            ->required()
                            ->getUploadedFileUsing(function ($record, $file) {
                                // При редактировании показываем существующее изображение
                                if ($record && $record->image) {
                                    return $record->image->url;
                                }
                                return null;
                            }),
                        TextInput::make('image_alt')
                            ->label('Альт (отображать, если изображение не загрузилось)')
                            ->required(),
                        TextInput::make('image_title')
                            ->label('Название')
                            ->required(),
                    ]),
            ]);
    }
}
