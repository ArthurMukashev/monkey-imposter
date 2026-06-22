<?php

namespace App\Filament\Resources\Places\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\{FileUpload,
    Repeater,
    Select,
    TextInput,
    Textarea,
    RichEditor,
    DatePicker,
    TimePicker,
    Toggle
};
use Filament\Schemas\Components\Section;

class PlaceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Основное')
                    ->schema([
                        Select::make('section')
                            ->options([
                                'tourism' => 'Туризм',
                                'active' => 'Активный отдых',
                                'gastronomy' => 'Гастрономия',
                            ])
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn(callable $set) => $set('category_id', null)), // сброс категории
                        TextInput::make('title')->required()->live()
                            ->afterStateUpdated(fn($state, callable $set) => $set('slug', \Str::slug($state))),
                        TextInput::make('slug')->required()->unique(ignoreRecord: true),
                        Textarea::make('short_description')->required()->maxLength(255),
                        RichEditor::make('description_html')
//                            ->toolbarButtons(['bold', 'italic', 'underline', 'strike', 'h2', 'h3', 'h4', 'link', 'blockquote', 'code-block', 'orderedList', 'bulletList', 'clean'])
                            ->disableToolbarButtons(['h1', 'image', 'video']),
                    ]),
                Section::make('Привязки')
                    ->schema([
                        Select::make('category_id')
                            ->relationship('category', 'title')
                            ->searchable()
                            ->required()
                            ->options(fn(callable $get) => \App\Models\Category::where('section', $get('section'))->pluck('title', 'id')),
                        Select::make('city_id')
                            ->relationship('city', 'title')
                            ->searchable()
                            ->required(),
                    ]),

                Section::make('Локация')
                    ->schema([
                        TextInput::make('latitude')->numeric()->step(0.0000001),
                        TextInput::make('longitude')->numeric()->step(0.0000001),
                        TextInput::make('address')->maxLength(255),
                    ]),

                Section::make('Гастрономия')
                    ->schema([
                        TextInput::make('working_hours')->maxLength(255),
                        TextInput::make('average_bill')->maxLength(255),
                        RichEditor::make('menu_html')
                            ->toolbarButtons(['bold', 'italic', 'underline', 'orderedList', 'bulletList', 'link'])
                            ->disableToolbarButtons(['image', 'video', 'h1', 'h2', 'h3']),
                        Select::make('cuisineTypes')
                            ->relationship('cuisineTypes', 'title')
                            ->multiple()
                            ->searchable(),
                    ])
                    ->visible(fn(callable $get) => $get('section') === 'gastronomy'),

                Section::make('Расписание')
                    ->schema([
                        DatePicker::make('schedule.date')->displayFormat('Y-m-d'),
                        TimePicker::make('schedule.time')->displayFormat('H:i'),
                        TextInput::make('schedule.timezone')
                            ->default('+05:00')
                            ->maxLength(6),
                    ])
                    ->visible(fn(callable $get) => in_array($get('section'), ['tourism', 'active'])),

                Section::make('Теги')
                    ->schema([
                        Select::make('tags')
                            ->relationship('tags', 'title')
                            ->multiple()
                            ->searchable(),
                    ]),

                Section::make('SEO')
                    ->schema([
                        TextInput::make('seo_title')->maxLength(255),
                        TextInput::make('seo_description')->maxLength(255),
                        TextInput::make('seo_canonical_path')->maxLength(255),
                    ]),

                Toggle::make('is_published')->label('Опубликовано'),

                Section::make('Изображения')
                    ->schema([
                        Repeater::make('images')
                            ->relationship('images')
                            ->schema([
                                FileUpload::make('url')
                                    ->image()
                                    ->directory('places')
                                    ->required(),
                                TextInput::make('alt')->required(),
                                TextInput::make('title')->required(),
                                Toggle::make('is_cover')->label('Обложка'),
                                TextInput::make('sort_order')->numeric()->default(0),
                            ])
                            ->orderColumn('sort_order')
                            ->defaultItems(1)
                            ->maxItems(20),
                    ]),
            ]);
    }
}
