<?php

namespace App\Filament\Resources\Places\Schemas;

use App\Models\Category;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PlaceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Основное')
                    ->schema([
                        Select::make('section')
                            ->label('Категория')
                            ->options([
                                'tourism' => 'Туризм',
                                'active' => 'Активный отдых',
                                'gastronomy' => 'Гастрономия',
                            ])
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn (callable $set) => $set('category_id', null)), // сброс категории
                        TextInput::make('title')
                            ->label('Название')
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', \Str::slug($state))),
                        TextInput::make('slug')->required()->unique(ignoreRecord: true),
                        Textarea::make('short_description')
                            ->label('Краткое описание')
                            ->required()
                            ->maxLength(255),
                        RichEditor::make('description_html')
                            ->label('Описание')
                            ->disableToolbarButtons(['h1', 'image', 'video']),
                    ]),
                Section::make('Привязки')
                    ->schema([
                        Select::make('category_id')
                            ->label('Категория')
                            ->relationship('category', 'title')
                            ->searchable()
                            ->required()
                            ->options(fn (callable $get) => Category::where('section', $get('section'))->pluck('title', 'id')),
                        Select::make('city_id')
                            ->label('Город')
                            ->relationship('city', 'title')
                            ->searchable()
                            ->required(),
                    ]),

                Section::make('Локация')
                    ->schema([
                        TextInput::make('latitude')
                            ->label('Ширина')
                            ->numeric()
                            ->step(0.0000001),
                        TextInput::make('longitude')
                            ->label('Долгота')
                            ->numeric()
                            ->step(0.0000001),
                        TextInput::make('address')
                            ->label('Адрес')
                            ->maxLength(255),
                    ]),

                Section::make('Гастрономия')
                    ->schema([
                        TextInput::make('working_hours')
                            ->label('Рабочее время')
                            ->maxLength(255),
                        TextInput::make('average_bill')
                            ->label('Средний чек')
                            ->maxLength(255),
                        RichEditor::make('menu_html')
                            ->label('Меню')
                            ->toolbarButtons(['bold', 'italic', 'underline', 'orderedList', 'bulletList', 'link'])
                            ->disableToolbarButtons(['image', 'video', 'h1', 'h2', 'h3']),
                        Select::make('cuisineTypes')
                            ->label('Кухни')
                            ->relationship('cuisineTypes', 'title')
                            ->multiple()
                            ->searchable(),
                    ])
                    ->visible(fn (callable $get) => $get('section') === 'gastronomy'),

                Section::make('Расписание')
                    ->schema([
                        DatePicker::make('schedule.date')
                            ->label('Дата')
                            ->displayFormat('Y-m-d'),
                        TimePicker::make('schedule.time')
                            ->label('Время')
                            ->displayFormat('H:i'),
                        DatePicker::make('schedule.endDate')
                            ->label('Дата окончания')
                            ->displayFormat('Y-m-d'),
                        TimePicker::make('schedule.endTime')
                            ->label('Время окончания')
                            ->displayFormat('H:i'),
                        TextInput::make('schedule.timezone')
                            ->label('Часовой пояс')
                            ->default('+05:00')
                            ->maxLength(6),
                    ])
                    ->visible(fn (callable $get) => in_array($get('section'), ['tourism', 'active'])),

                Section::make('Теги')
                    ->schema([
                        Select::make('tags')
                            ->label('Теги')
                            ->relationship('tags', 'title')
                            ->multiple()
                            ->searchable(),
                    ]),

                Section::make('SEO')
                    ->schema([
                        TextInput::make('seo_title')
                            ->label('SEO заголовок')
                            ->maxLength(255),
                        TextInput::make('seo_description')
                            ->label('SEO описание')
                            ->maxLength(255),
                        TextInput::make('seo_canonical_path')
                            ->label('SEO канонический путь')
                            ->maxLength(255),
                    ]),

                Toggle::make('is_published')->label('Опубликовано'),

                Section::make('Изображения')
                    ->schema([
                        Repeater::make('images')
                            ->label('Изображения')
                            ->relationship('images')
                            ->schema([
                                FileUpload::make('url')
                                    ->label('URL')
                                    ->image()
                                    ->directory('places')
                                    ->required(),
                                TextInput::make('alt')
                                    ->label('Альт (отображать, если изображение не загрузилось)')
                                    ->required(),
                                TextInput::make('title')
                                    ->label('Название')
                                    ->required(),
                                Toggle::make('is_cover')->label('Обложка'),
                                TextInput::make('sort_order')
                                    ->label('Порядок сортировки')
                                    ->numeric()
                                    ->default(0),
                            ])
                            ->orderColumn('sort_order')
                            ->defaultItems(1)
                            ->maxItems(20),
                    ]),
            ]);
    }
}
